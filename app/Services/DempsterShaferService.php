<?php

namespace App\Services;

use App\Models\KnowledgeRule;
use App\Models\MentalDisorder;
use App\Models\Symptom;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Throwable;

class DempsterShaferService
{
    private const THETA = '__theta__';

    /**
     * @var array<string, array{code: string, name: string, solution: string}>
     */
    private array $disorders;

    /**
     * @var array<string, array{code: string, name: string, belief: float, disorders: list<string>}>
     */
    private array $knowledgeBase;

    /**
     * @var array<string, int>
     */
    private array $symptomOrder;

    public function __construct(?array $knowledgeBase = null, ?array $disorders = null)
    {
        $databaseData = ($knowledgeBase === null && $disorders === null)
            ? $this->databaseKnowledgeBase()
            : null;

        $this->disorders = $disorders ?? $databaseData['disorders'] ?? $this->defaultDisorders();
        $this->knowledgeBase = $knowledgeBase ?? $databaseData['knowledgeBase'] ?? $this->defaultKnowledgeBase();
        $this->symptomOrder = array_flip(array_keys($this->knowledgeBase));
    }

    public function diagnose(Collection|array $symptoms): array
    {
        [$selectedCodes, $unknownCodes] = $this->resolveSelectedCodes($symptoms);
        $selectedSymptoms = array_map(fn (string $code): array => $this->formatSymptom($code), $selectedCodes);

        if ($selectedCodes === []) {
            return [
                'result' => null,
                'disorder' => null,
                'belief' => 0.0,
                'percentage' => 0.0,
                'percentage_text' => '0%',
                'certainty' => 'Kurang Pasti',
                'certainty_label' => 'Kurang Pasti',
                'masses' => [],
                'steps' => [],
                'selected_symptoms' => [],
                'unknown_symptoms' => $unknownCodes,
            ];
        }

        $currentMass = [];
        $steps = [];

        foreach ($selectedCodes as $index => $code) {
            $symptom = $this->knowledgeBase[$code];
            $hypothesisKey = $this->focusKey($symptom['disorders']);
            $evidenceMass = $this->cleanMass([
                $hypothesisKey => $symptom['belief'],
                self::THETA => 1 - $symptom['belief'],
            ]);

            if ($index === 0) {
                $currentMass = $evidenceMass;
                $steps[] = [
                    'step' => 1,
                    'symptom' => $this->formatSymptom($code),
                    'evidence_mass' => $this->formatMass($evidenceMass),
                    'conflict' => 0.0,
                    'normalizer' => 1.0,
                    'result_mass' => $this->formatMass($currentMass),
                    'description' => sprintf(
                        '%s memberikan massa %s sebesar %.3f dan Theta sebesar %.3f.',
                        $code,
                        $this->formatFocusLabel($hypothesisKey),
                        $symptom['belief'],
                        1 - $symptom['belief'],
                    ),
                ];

                continue;
            }

            $combined = $this->combineMasses($currentMass, $evidenceMass, $symptom['disorders']);
            $currentMass = $combined['mass'];

            $steps[] = [
                'step' => $index + 1,
                'symptom' => $this->formatSymptom($code),
                'previous_mass' => $this->formatMass($combined['previous_mass']),
                'evidence_mass' => $this->formatMass($evidenceMass),
                'raw_mass' => $this->formatMass($combined['raw_mass']),
                'conflict' => $this->roundValue($combined['conflict']),
                'normalizer' => $this->roundValue($combined['normalizer']),
                'result_mass' => $this->formatMass($currentMass),
                'description' => sprintf(
                    'Kombinasi dengan %s menghasilkan massa dominan %s sebesar %.3f.',
                    $code,
                    $this->formatFocusLabel($this->dominantMassKey($currentMass)),
                    $this->dominantBelief($currentMass),
                ),
            ];
        }

        $dominantKey = $this->dominantMassKey($currentMass);
        $belief = $this->roundValue($this->dominantBelief($currentMass));
        $percentage = $this->roundPercentage($belief);
        $certaintyLabel = $this->certaintyLabel($belief);
        $result = $this->formatResult($dominantKey);

        return [
            'result' => $result,
            'disorder' => $result['name'],
            'belief' => $belief,
            'percentage' => $percentage,
            'percentage_text' => $this->formatPercentageText($percentage),
            'certainty' => $certaintyLabel,
            'certainty_label' => $certaintyLabel,
            'masses' => $this->formatMass($currentMass),
            'steps' => $steps,
            'selected_symptoms' => $selectedSymptoms,
            'unknown_symptoms' => $unknownCodes,
        ];
    }

    public function symptoms(): array
    {
        return array_values(array_map(
            fn (array $symptom): array => $this->formatSymptom($symptom['code']),
            $this->knowledgeBase,
        ));
    }

    public function disorders(): array
    {
        return array_values($this->disorders);
    }

    public function knowledgeBase(): array
    {
        return $this->knowledgeBase;
    }

    private function combineMasses(array $currentMass, array $evidenceMass, array $incomingHypotheses): array
    {
        $rawMass = [];
        $conflict = 0.0;

        foreach ($currentMass as $currentFocus => $currentValue) {
            foreach ($evidenceMass as $evidenceFocus => $evidenceValue) {
                $product = $currentValue * $evidenceValue;
                $intersection = $this->intersectFocus($currentFocus, $evidenceFocus);

                if ($intersection === null) {
                    $conflict += $product;

                    continue;
                }

                $rawMass[$intersection] = ($rawMass[$intersection] ?? 0.0) + $product;
            }
        }

        $normalizer = 1 - $conflict;

        if ($normalizer <= 0.0) {
            return [
                'previous_mass' => $currentMass,
                'raw_mass' => [],
                'mass' => [self::THETA => 1.0],
                'conflict' => 1.0,
                'normalizer' => 0.0,
            ];
        }

        foreach ($rawMass as $focus => $value) {
            $rawMass[$focus] = $value / $normalizer;
        }

        $mass = $this->collapseToIncomingHypothesis($rawMass, $incomingHypotheses);

        return [
            'previous_mass' => $currentMass,
            'raw_mass' => $this->cleanMass($rawMass),
            'mass' => $mass,
            'conflict' => $conflict,
            'normalizer' => $normalizer,
        ];
    }

    private function collapseToIncomingHypothesis(array $rawMass, array $incomingHypotheses): array
    {
        // The source document's worked example groups all non-Theta mass into the
        // newest symptom hypothesis after each combination.
        $incomingKey = $this->focusKey($incomingHypotheses);
        $collapsed = [
            $incomingKey => 0.0,
            self::THETA => 0.0,
        ];

        foreach ($rawMass as $focus => $value) {
            if ($focus === self::THETA) {
                $collapsed[self::THETA] += $value;

                continue;
            }

            $collapsed[$incomingKey] += $value;
        }

        return $this->cleanMass($collapsed);
    }

    private function resolveSelectedCodes(Collection|array $symptoms): array
    {
        $items = $symptoms instanceof Collection ? $symptoms->all() : $symptoms;
        $codes = [];
        $unknownCodes = [];

        foreach ($items as $key => $value) {
            $code = $this->extractCode($key, $value);

            if ($code === null) {
                continue;
            }

            $code = $this->normalizeSymptomCode($code);

            if ($code === null || in_array($code, $codes, true)) {
                continue;
            }

            if (! array_key_exists($code, $this->knowledgeBase)) {
                $unknownCodes[] = $code;

                continue;
            }

            $codes[] = $code;
        }

        usort($codes, fn (string $left, string $right): int => $this->symptomOrder[$left] <=> $this->symptomOrder[$right]);

        return [$codes, $unknownCodes];
    }

    private function extractCode(int|string $key, mixed $value): ?string
    {
        if (is_string($value) || is_int($value)) {
            return (string) $value;
        }

        if (is_bool($value)) {
            return $value && is_string($key) ? $key : null;
        }

        if (is_array($value)) {
            foreach (['code', 'kode', 'kd_gejala', 'symptom_code', 'gejala'] as $field) {
                if (isset($value[$field]) && (is_string($value[$field]) || is_int($value[$field]))) {
                    return (string) $value[$field];
                }
            }
        }

        if (is_object($value)) {
            foreach (['code', 'kode', 'kd_gejala', 'symptom_code', 'gejala'] as $field) {
                if (isset($value->{$field}) && (is_string($value->{$field}) || is_int($value->{$field}))) {
                    return (string) $value->{$field};
                }
            }
        }

        return null;
    }

    private function normalizeSymptomCode(string $code): ?string
    {
        $code = strtoupper(trim($code));

        if ($code === '') {
            return null;
        }

        if (preg_match('/^G(\d+)$/', $code, $matches) === 1) {
            return sprintf('G%02d', (int) $matches[1]);
        }

        if (preg_match('/^\d+$/', $code) === 1) {
            return sprintf('G%02d', (int) $code);
        }

        return $code;
    }

    private function intersectFocus(string $left, string $right): ?string
    {
        if ($left === self::THETA) {
            return $right;
        }

        if ($right === self::THETA) {
            return $left;
        }

        $intersection = array_values(array_intersect($this->parseFocusKey($left), $this->parseFocusKey($right)));

        return $intersection === [] ? null : $this->focusKey($intersection);
    }

    private function dominantMassKey(array $mass): string
    {
        $dominantKey = self::THETA;
        $dominantValue = -1.0;

        foreach ($mass as $focus => $value) {
            if ($focus === self::THETA && $dominantKey !== self::THETA && $value === $dominantValue) {
                continue;
            }

            if ($value > $dominantValue) {
                $dominantKey = $focus;
                $dominantValue = $value;
            }
        }

        return $dominantKey;
    }

    private function dominantBelief(array $mass): float
    {
        return $mass[$this->dominantMassKey($mass)] ?? 0.0;
    }

    private function formatSymptom(string $code): array
    {
        $symptom = $this->knowledgeBase[$code];

        return [
            'code' => $symptom['code'],
            'name' => $symptom['name'],
            'belief' => $symptom['belief'],
            'disorders' => array_map(fn (string $disorderCode): array => $this->disorders[$disorderCode], $symptom['disorders']),
        ];
    }

    private function formatResult(string $focus): array
    {
        if ($focus === self::THETA) {
            return [
                'code' => 'Theta',
                'name' => 'Tidak diketahui',
                'hypotheses' => array_values($this->disorders),
            ];
        }

        $codes = $this->parseFocusKey($focus);
        $hypotheses = array_map(fn (string $code): array => $this->disorders[$code], $codes);

        return [
            'code' => implode(',', $codes),
            'name' => implode(' / ', array_column($hypotheses, 'name')),
            'hypotheses' => $hypotheses,
        ];
    }

    private function formatMass(array $mass): array
    {
        $formatted = [];

        foreach ($this->sortMass($mass) as $focus => $value) {
            $formatted[$this->formatFocusLabel($focus)] = $this->roundValue($value);
        }

        return $formatted;
    }

    private function formatFocusLabel(string $focus): string
    {
        return $focus === self::THETA ? 'Theta' : str_replace('|', ',', $focus);
    }

    private function focusKey(array $codes): string
    {
        $codes = array_values(array_unique(array_map(fn (string $code): string => strtoupper($code), $codes)));
        $order = array_flip(array_keys($this->disorders));

        usort($codes, fn (string $left, string $right): int => ($order[$left] ?? PHP_INT_MAX) <=> ($order[$right] ?? PHP_INT_MAX));

        return implode('|', $codes);
    }

    private function parseFocusKey(string $focus): array
    {
        return $focus === self::THETA ? array_keys($this->disorders) : explode('|', $focus);
    }

    private function cleanMass(array $mass): array
    {
        $clean = [];

        foreach ($mass as $focus => $value) {
            if (abs($value) < 0.000000000001) {
                continue;
            }

            $clean[$focus] = $value;
        }

        return $this->sortMass($clean);
    }

    private function sortMass(array $mass): array
    {
        $order = array_flip(array_keys($this->disorders));

        uksort($mass, function (string $left, string $right) use ($order): int {
            if ($left === self::THETA) {
                return 1;
            }

            if ($right === self::THETA) {
                return -1;
            }

            $leftCodes = $this->parseFocusKey($left);
            $rightCodes = $this->parseFocusKey($right);

            $leftOrder = min(array_map(fn (string $code): int => $order[$code] ?? PHP_INT_MAX, $leftCodes));
            $rightOrder = min(array_map(fn (string $code): int => $order[$code] ?? PHP_INT_MAX, $rightCodes));

            return $leftOrder === $rightOrder ? count($leftCodes) <=> count($rightCodes) : $leftOrder <=> $rightOrder;
        });

        return $mass;
    }

    private function certaintyLabel(float $belief): string
    {
        return match (true) {
            $belief >= 1.0 => 'Sangat Pasti',
            $belief >= 0.75 => 'Pasti',
            $belief >= 0.50 => 'Cukup Pasti',
            default => 'Kurang Pasti',
        };
    }

    private function roundValue(float $value): float
    {
        return round($value, 6);
    }

    private function roundPercentage(float $belief): float
    {
        return round($belief * 100, 1);
    }

    private function formatPercentageText(float $percentage): string
    {
        $text = rtrim(rtrim(number_format($percentage, 1, '.', ''), '0'), '.');

        return $text.'%';
    }

    /**
     * @return array{disorders: array<string, array{code: string, name: string, solution: string|null}>, knowledgeBase: array<string, array{code: string, name: string, belief: float, disorders: list<string>}>}|null
     */
    private function databaseKnowledgeBase(): ?array
    {
        try {
            if (! Schema::hasTable('mental_disorders') || ! Schema::hasTable('symptoms') || ! Schema::hasTable('knowledge_rules')) {
                return null;
            }

            $disorders = MentalDisorder::query()
                ->orderBy('code')
                ->get()
                ->mapWithKeys(fn (MentalDisorder $disorder): array => [
                    $disorder->code => [
                        'code' => $disorder->code,
                        'name' => $this->disorderDisplayName($disorder),
                        'solution' => $disorder->solution,
                    ],
                ])
                ->all();

            if ($disorders === []) {
                return null;
            }

            $rulesBySymptom = KnowledgeRule::query()
                ->with(['symptom', 'mentalDisorder'])
                ->get()
                ->filter(fn (KnowledgeRule $rule): bool => $rule->symptom !== null && $rule->mentalDisorder !== null)
                ->groupBy(fn (KnowledgeRule $rule): string => $rule->symptom->code);

            $knowledgeBase = Symptom::query()
                ->orderBy('code')
                ->get()
                ->mapWithKeys(function (Symptom $symptom) use ($rulesBySymptom): array {
                    $rules = $rulesBySymptom->get($symptom->code, collect());

                    if ($rules->isEmpty()) {
                        return [];
                    }

                    return [
                        $symptom->code => [
                            'code' => $symptom->code,
                            'name' => $symptom->name,
                            'belief' => (float) $rules->max('belief'),
                            'disorders' => $rules
                                ->map(fn (KnowledgeRule $rule): string => $rule->mentalDisorder->code)
                                ->unique()
                                ->values()
                                ->all(),
                        ],
                    ];
                })
                ->all();

            return $knowledgeBase === [] ? null : [
                'disorders' => $disorders,
                'knowledgeBase' => $knowledgeBase,
            ];
        } catch (Throwable) {
            return null;
        }
    }

    private function disorderDisplayName(MentalDisorder $disorder): string
    {
        if (filled($disorder->scientific_name)) {
            return "{$disorder->name} ({$disorder->scientific_name})";
        }

        return $disorder->name;
    }

    private function defaultDisorders(): array
    {
        return [
            'P01' => [
                'code' => 'P01',
                'name' => 'Depresi (depressive disorder)',
                'solution' => 'Segera hubungi psikolog',
            ],
            'P02' => [
                'code' => 'P02',
                'name' => 'Gangguan kecemasan (anxiety)',
                'solution' => 'Melakukan terapi seperti cognitive behavioral therapy (CBT)',
            ],
        ];
    }

    private function defaultKnowledgeBase(): array
    {
        return [
            'G01' => ['code' => 'G01', 'name' => 'Tindakan ingin bunuh diri', 'belief' => 0.8, 'disorders' => ['P01']],
            'G02' => ['code' => 'G02', 'name' => 'Malas berkomunikasi', 'belief' => 0.3, 'disorders' => ['P01']],
            'G03' => ['code' => 'G03', 'name' => 'Menarik diri dari keluarga', 'belief' => 0.6, 'disorders' => ['P01']],
            'G04' => ['code' => 'G04', 'name' => 'Mudah marah', 'belief' => 0.5, 'disorders' => ['P01']],
            'G05' => ['code' => 'G05', 'name' => 'Merasa cemas berlebih', 'belief' => 0.4, 'disorders' => ['P01']],
            'G06' => ['code' => 'G06', 'name' => 'Tidak memiliki rasa percaya diri', 'belief' => 0.3, 'disorders' => ['P01']],
            'G07' => ['code' => 'G07', 'name' => 'Sedih berkepanjangan', 'belief' => 0.4, 'disorders' => ['P01']],
            'G08' => ['code' => 'G08', 'name' => 'Overthinking', 'belief' => 0.5, 'disorders' => ['P01', 'P02']],
            'G09' => ['code' => 'G09', 'name' => 'Sering menderita sakit kepala', 'belief' => 0.3, 'disorders' => ['P02']],
            'G10' => ['code' => 'G10', 'name' => 'Tidak memiliki nafsu makan', 'belief' => 0.3, 'disorders' => ['P02']],
            'G11' => ['code' => 'G11', 'name' => 'Sulit tidur', 'belief' => 0.4, 'disorders' => ['P02']],
            'G12' => ['code' => 'G12', 'name' => 'Mudah takut', 'belief' => 0.2, 'disorders' => ['P02']],
            'G13' => ['code' => 'G13', 'name' => 'Pencernaan terganggu/buruk', 'belief' => 0.3, 'disorders' => ['P02']],
            'G14' => ['code' => 'G14', 'name' => 'Sulit berpikir jernih', 'belief' => 0.5, 'disorders' => ['P02']],
            'G15' => ['code' => 'G15', 'name' => 'Merasa tidak bahagia', 'belief' => 0.5, 'disorders' => ['P02']],
        ];
    }
}
