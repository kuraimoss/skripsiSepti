<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreConsultationRequest;
use App\Models\Consultation;
use App\Models\Disorder;
use App\Models\MentalDisorder;
use App\Models\Symptom;
use App\Services\DempsterShaferService;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;
use JsonSerializable;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionUnionType;
use RuntimeException;

class ConsultationController extends Controller
{
    /**
     * @var array<string, array<int, string>>
     */
    private array $columnCache = [];

    /**
     * Function ini digunakan untuk menampilkan form konsultasi
     * dengan daftar gejala aktif yang dapat dipilih oleh user.
     */
    public function create(): View
    {
        $query = Symptom::query();

        if ($activeColumn = $this->firstExistingColumn(Symptom::class, ['is_active', 'active'])) {
            $query->where($activeColumn, true);
        }

        if ($orderColumn = $this->firstExistingColumn(Symptom::class, ['code', 'kode', 'name', 'nama', 'created_at', 'id'])) {
            $query->orderBy($orderColumn);
        }

        return view('consultation.create', [
            'symptoms' => $query->get(),
        ]);
    }

    /**
     * Function ini digunakan untuk memproses konsultasi user,
     * menjalankan perhitungan Dempster-Shafer, lalu menyimpan hasilnya.
     */
    public function store(
        StoreConsultationRequest $request,
        DempsterShaferService $dempsterShaferService
    ): RedirectResponse {
        $validated = $request->validated();
        $symptomIds = $this->normalizeSymptomIds($validated['symptoms']);
        $symptoms = Symptom::query()->whereKey($symptomIds)->get();

        if ($symptoms->count() !== count($symptomIds)) {
            return back()
                ->withErrors(['symptoms' => 'Sebagian gejala yang dipilih tidak valid.'])
                ->withInput();
        }

        $selectedSymptoms = $this->selectedSymptomsSnapshot($symptoms, $symptomIds);
        $diagnosis = $this->runDempsterShafer($dempsterShaferService, $symptomIds, $symptoms, $validated);
        $resultSnapshot = $this->normalizeSnapshot($diagnosis);

        $consultation = DB::transaction(function () use ($validated, $symptomIds, $selectedSymptoms, $resultSnapshot): Consultation {
            $consultation = Consultation::forceCreate(
                $this->consultationPayload($validated, $symptomIds, $selectedSymptoms, $resultSnapshot)
            );

            $this->persistSelectedSymptoms($consultation, $selectedSymptoms);
            $this->persistConsultationResults($consultation, $resultSnapshot);

            return $consultation;
        });

        return redirect()->route('consultation.show', $consultation);
    }

    /**
     * Function ini digunakan untuk menampilkan hasil konsultasi
     * yang sudah tersimpan di database.
     */
    public function show(Consultation $consultation): View
    {
        $this->loadKnownRelations($consultation);

        return view('consultation.show', [
            'consultation' => $consultation,
        ]);
    }

    /**
     * Function ini digunakan untuk menampilkan versi cetak
     * dari hasil konsultasi yang dipilih user.
     */
    public function printResult(Consultation $consultation): View
    {
        $this->loadKnownRelations($consultation);

        return view('consultation.print', [
            'consultation' => $consultation,
        ]);
    }

    /**
     * Function ini digunakan untuk merapikan daftar ID gejala
     * agar unik, berbentuk angka, dan siap diproses.
     *
     * @param  array<int, mixed>  $symptoms
     * @return array<int, int>
     */
    private function normalizeSymptomIds(array $symptoms): array
    {
        return collect($symptoms)
            ->map(fn (mixed $symptomId): int => (int) $symptomId)
            ->unique()
            ->values()
            ->all();
    }

    /**
     * Function ini digunakan untuk membuat salinan data gejala terpilih
     * sesuai urutan pilihan user sebelum disimpan ke riwayat konsultasi.
     *
     * @param  Collection<int, Symptom>  $symptoms
     * @param  array<int, int>  $symptomIds
     * @return array<int, array<string, mixed>>
     */
    private function selectedSymptomsSnapshot(Collection $symptoms, array $symptomIds): array
    {
        $symptomsById = $symptoms->keyBy(fn (Symptom $symptom): int|string => $symptom->getKey());

        return collect($symptomIds)
            ->map(fn (int $symptomId): ?Symptom => $symptomsById->get($symptomId))
            ->filter()
            ->map(fn (Symptom $symptom): array => [
                'id' => $symptom->getKey(),
                'code' => $this->firstAttribute($symptom, ['code', 'kode']),
                'name' => $this->firstAttribute($symptom, ['name', 'nama', 'title']),
                'belief' => $this->firstAttribute($symptom, ['belief', 'density', 'weight', 'bobot']),
            ])
            ->values()
            ->all();
    }

    /**
     * Function ini digunakan untuk menjalankan service Dempster-Shafer
     * dengan method perhitungan yang tersedia pada service.
     *
     * @param  array<string, mixed>  $patient
     * @param  array<int, int>  $symptomIds
     * @param  Collection<int, Symptom>  $symptoms
     */
    private function runDempsterShafer(
        DempsterShaferService $service,
        array $symptomIds,
        Collection $symptoms,
        array $patient
    ): mixed {
        foreach (['diagnose', 'calculate', 'analyze', 'process'] as $method) {
            if (method_exists($service, $method)) {
                return $service->{$method}(...$this->serviceArguments($service, $method, $symptomIds, $symptoms, $patient));
            }
        }

        throw new RuntimeException('DempsterShaferService harus menyediakan method diagnose, calculate, analyze, atau process.');
    }

    /**
     * Function ini digunakan untuk menyiapkan argument yang sesuai
     * dengan parameter method pada service Dempster-Shafer.
     *
     * @param  array<int, int>  $symptomIds
     * @param  Collection<int, Symptom>  $symptoms
     * @param  array<string, mixed>  $patient
     * @return array<int, mixed>
     */
    private function serviceArguments(
        DempsterShaferService $service,
        string $method,
        array $symptomIds,
        Collection $symptoms,
        array $patient
    ): array {
        $symptomCodes = $this->selectedSymptomCodes($symptoms, $symptomIds);

        return collect((new ReflectionMethod($service, $method))->getParameters())
            ->map(function ($parameter) use ($symptomIds, $symptoms, $symptomCodes, $patient): mixed {
                $type = $parameter->getType();
                $name = strtolower($parameter->getName());

                if ($type instanceof ReflectionNamedType && is_a($type->getName(), Collection::class, true)) {
                    return $symptoms;
                }

                if ($type instanceof ReflectionUnionType) {
                    foreach ($type->getTypes() as $unionType) {
                        if ($unionType instanceof ReflectionNamedType && is_a($unionType->getName(), Collection::class, true)) {
                            return $symptoms;
                        }
                    }
                }

                if (str_contains($name, 'patient') || str_contains($name, 'pasien')) {
                    return $patient;
                }

                if (str_contains($name, 'id')) {
                    return $symptomIds;
                }

                if (str_contains($name, 'model') || str_contains($name, 'collection')) {
                    return $symptoms;
                }

                if (str_contains($name, 'symptom') || str_contains($name, 'gejala') || str_contains($name, 'code')) {
                    return $symptomCodes;
                }

                return $symptomCodes;
            })
            ->all();
    }

    /**
     * Function ini digunakan untuk mengambil kode gejala
     * dari daftar ID gejala yang dipilih user.
     *
     * @param  Collection<int, Symptom>  $symptoms
     * @param  array<int, int>  $symptomIds
     * @return array<int, int|string>
     */
    private function selectedSymptomCodes(Collection $symptoms, array $symptomIds): array
    {
        $symptomsById = $symptoms->keyBy(fn (Symptom $symptom): int|string => $symptom->getKey());

        return collect($symptomIds)
            ->map(function (int $symptomId) use ($symptomsById): int|string {
                $symptom = $symptomsById->get($symptomId);

                return $symptom?->getAttribute('code') ?? $symptomId;
            })
            ->values()
            ->all();
    }

    /**
     * Function ini digunakan untuk mengubah hasil perhitungan
     * menjadi array agar mudah disimpan dan ditampilkan.
     *
     * @return array<string, mixed>
     */
    private function normalizeSnapshot(mixed $value): array
    {
        if ($value instanceof Arrayable) {
            return $value->toArray();
        }

        if ($value instanceof JsonSerializable) {
            $value = $value->jsonSerialize();
        }

        if ($value instanceof Collection) {
            return $value->toArray();
        }

        if (is_array($value)) {
            return $value;
        }

        if (is_object($value)) {
            return get_object_vars($value);
        }

        return ['value' => $value];
    }

    /**
     * Function ini digunakan untuk menyusun data konsultasi
     * yang akan disimpan ke tabel konsultasi.
     *
     * @param  array<string, mixed>  $patient
     * @param  array<int, int>  $symptomIds
     * @param  array<int, array<string, mixed>>  $selectedSymptoms
     * @param  array<string, mixed>  $resultSnapshot
     * @return array<string, mixed>
     */
    private function consultationPayload(
        array $patient,
        array $symptomIds,
        array $selectedSymptoms,
        array $resultSnapshot
    ): array {
        $disorderId = $this->resolveDisorderId($this->firstResultValue($resultSnapshot, [
            'detected_mental_disorder_id',
            'mental_disorder_id',
            'disorder_id',
            'diagnosis_disorder_id',
            'result_disorder_id',
            'result.code',
            'disorder.id',
            'diagnosis.id',
            'diagnosis.code',
            'diagnosis.mental_disorder_id',
            'diagnosis.disorder_id',
            'best_disorder.id',
            'best_disorder.code',
            'best_disorder.disorder_id',
            'result.disorder_id',
            'result.disorder.id',
        ]));

        $confidence = $this->firstResultValue($resultSnapshot, [
            'belief',
            'confidence',
            'score',
            'percentage',
            'nilai_kepercayaan',
            'diagnosis.belief',
            'diagnosis.confidence',
            'best_disorder.belief',
            'best_disorder.confidence',
            'result.belief',
            'result.confidence',
        ]);
        $certaintyLabel = $this->firstResultValue($resultSnapshot, ['certainty_label', 'certainty'])
            ?? $this->certaintyLabel($confidence);

        $candidates = [
            'code' => $this->newConsultationCode(),
            'respondent_name' => $patient['name'],
            'respondent_age' => $patient['age'] ?? null,
            'respondent_gender' => $patient['gender'],
            'respondent_address' => $patient['address'],
            'respondent_phone' => $patient['phone'],
            'started_at' => now(),
            'completed_at' => now(),
            'detected_mental_disorder_id' => $disorderId,
            'confidence_score' => $this->confidenceScore($confidence),
            'confidence_percentage' => $this->confidencePercentage($confidence),
            'certainty_label' => $certaintyLabel,
            'mass_values' => $resultSnapshot,
            'notes' => $this->patientNotes($patient),
            'patient_name' => $patient['name'],
            'nama' => $patient['name'],
            'name' => $patient['name'],
            'gender' => $patient['gender'],
            'kelamin' => $patient['gender'],
            'jenis_kelamin' => $patient['gender'],
            'address' => $patient['address'],
            'alamat' => $patient['address'],
            'phone' => $patient['phone'],
            'telephone' => $patient['phone'],
            'telepon' => $patient['phone'],
            'no_hp' => $patient['phone'],
            'phone_number' => $patient['phone'],
            'symptom_ids' => $symptomIds,
            'selected_symptom_ids' => $symptomIds,
            'gejala_ids' => $symptomIds,
            'selected_symptoms' => $selectedSymptoms,
            'selected_symptoms_snapshot' => $selectedSymptoms,
            'symptoms_snapshot' => $selectedSymptoms,
            'gejala_snapshot' => $selectedSymptoms,
            'result_snapshot' => $resultSnapshot,
            'diagnosis_snapshot' => $resultSnapshot,
            'hasil_snapshot' => $resultSnapshot,
            'dempster_shafer_result' => $resultSnapshot,
            'result' => $resultSnapshot,
            'disorder_id' => $disorderId,
            'diagnosis_disorder_id' => $disorderId,
            'result_disorder_id' => $disorderId,
            'belief' => $this->confidenceScore($confidence),
            'confidence' => $this->confidenceScore($confidence),
            'score' => $this->confidenceScore($confidence),
            'percentage' => $this->confidencePercentage($confidence),
            'nilai_kepercayaan' => $confidence,
        ];

        $payload = [];

        foreach ($candidates as $column => $value) {
            if ($value !== null && $this->hasColumn(Consultation::class, $column)) {
                $payload[$column] = $this->valueForStorage(Consultation::class, $column, $value);
            }
        }

        if ($payload !== []) {
            return $payload;
        }

        return [
            'code' => $this->newConsultationCode(),
            'patient_name' => $patient['name'],
            'respondent_name' => $patient['name'],
            'respondent_age' => $patient['age'] ?? null,
            'respondent_gender' => $patient['gender'],
            'respondent_address' => $patient['address'],
            'respondent_phone' => $patient['phone'],
            'gender' => $patient['gender'],
            'address' => $patient['address'],
            'phone' => $patient['phone'],
            'started_at' => now(),
            'completed_at' => now(),
            'symptom_ids' => $symptomIds,
            'selected_symptoms' => $selectedSymptoms,
            'result_snapshot' => $resultSnapshot,
            'disorder_id' => $disorderId,
            'detected_mental_disorder_id' => $disorderId,
            'belief' => $this->confidenceScore($confidence),
            'confidence_score' => $this->confidenceScore($confidence),
            'confidence_percentage' => $this->confidencePercentage($confidence),
            'certainty_label' => $certaintyLabel,
            'mass_values' => $resultSnapshot,
            'notes' => $this->patientNotes($patient),
        ];
    }

    /**
     * Function ini digunakan untuk menyimpan relasi gejala
     * yang dipilih pada satu konsultasi.
     *
     * @param  array<int, array<string, mixed>>  $selectedSymptoms
     */
    private function persistSelectedSymptoms(Consultation $consultation, array $selectedSymptoms): void
    {
        if (! method_exists($consultation, 'consultationSymptoms')) {
            return;
        }

        foreach ($selectedSymptoms as $index => $symptom) {
            $consultation->consultationSymptoms()->create([
                'symptom_id' => $symptom['id'],
                'belief' => $symptom['belief'] ?? 0,
                'selected' => true,
                'sort_order' => $index + 1,
            ]);
        }
    }

    /**
     * Function ini digunakan untuk menyimpan daftar hasil diagnosis
     * dari perhitungan Dempster-Shafer.
     *
     * @param  array<string, mixed>  $resultSnapshot
     */
    private function persistConsultationResults(Consultation $consultation, array $resultSnapshot): void
    {
        if (! method_exists($consultation, 'results')) {
            return;
        }

        foreach ($this->resultRows($resultSnapshot) as $row) {
            if ($row['mental_disorder_id'] !== null) {
                $consultation->results()->create($row);
            }
        }
    }

    /**
     * Function ini digunakan untuk membentuk baris hasil diagnosis
     * yang akan disimpan pada tabel hasil konsultasi.
     *
     * @param  array<string, mixed>  $resultSnapshot
     * @return array<int, array<string, mixed>>
     */
    private function resultRows(array $resultSnapshot): array
    {
        $results = $this->firstResultValue($resultSnapshot, ['results', 'diagnoses', 'rankings', 'beliefs', 'scores', 'disorders']);
        $rows = [];

        if (is_array($results)) {
            foreach ($results as $rank => $result) {
                $result = is_array($result) ? $result : [
                    'mental_disorder_id' => is_numeric($rank) ? null : $rank,
                    'belief' => $result,
                ];

                $confidence = $this->firstResultValue($result, [
                    'belief',
                    'confidence',
                    'score',
                    'percentage',
                    'nilai_kepercayaan',
                ]);

                $rows[] = [
                    'mental_disorder_id' => $this->resolveDisorderId($this->firstResultValue($result, [
                        'mental_disorder_id',
                        'disorder_id',
                        'id',
                        'code',
                        'disorder.id',
                        'disorder.code',
                        'mental_disorder.id',
                        'mental_disorder.code',
                    ])),
                    'belief' => $this->confidenceScore($confidence) ?? 0,
                    'percentage' => $this->confidencePercentage($confidence) ?? 0,
                    'rank' => is_numeric($rank) ? ((int) $rank) + 1 : count($rows) + 1,
                    'is_selected' => false,
                ];
            }
        }

        $selectedDisorderId = $this->resolveDisorderId($this->firstResultValue($resultSnapshot, [
            'detected_mental_disorder_id',
            'mental_disorder_id',
            'disorder_id',
            'diagnosis_disorder_id',
            'result_disorder_id',
            'result.code',
            'disorder.id',
            'disorder.code',
            'mental_disorder.id',
            'mental_disorder.code',
            'diagnosis.id',
            'diagnosis.code',
            'diagnosis.mental_disorder_id',
            'diagnosis.disorder_id',
            'best_disorder.id',
            'best_disorder.code',
            'best_disorder.disorder_id',
            'result.disorder_id',
            'result.disorder.id',
        ]));

        if ($selectedDisorderId !== null && collect($rows)->doesntContain('mental_disorder_id', $selectedDisorderId)) {
            $confidence = $this->firstResultValue($resultSnapshot, [
                'belief',
                'confidence',
                'score',
                'percentage',
                'nilai_kepercayaan',
                'diagnosis.belief',
                'diagnosis.confidence',
                'best_disorder.belief',
                'best_disorder.confidence',
                'result.belief',
                'result.confidence',
            ]);

            $rows[] = [
                'mental_disorder_id' => $selectedDisorderId,
                'belief' => $this->confidenceScore($confidence) ?? 0,
                'percentage' => $this->confidencePercentage($confidence) ?? 0,
                'rank' => count($rows) + 1,
                'is_selected' => true,
            ];
        }

        if ($selectedDisorderId !== null) {
            $rows = collect($rows)
                ->map(function (array $row) use ($selectedDisorderId): array {
                    $row['is_selected'] = (string) $row['mental_disorder_id'] === (string) $selectedDisorderId;

                    return $row;
                })
                ->values()
                ->all();
        }

        return $rows;
    }

    /**
     * Function ini digunakan untuk mengambil nilai pertama
     * dari beberapa kemungkinan nama atribut model.
     *
     * @param  array<int, string>  $attributes
     */
    private function firstAttribute(Model $model, array $attributes): mixed
    {
        foreach ($attributes as $attribute) {
            if ($model->getAttribute($attribute) !== null) {
                return $model->getAttribute($attribute);
            }
        }

        return null;
    }

    /**
     * Function ini digunakan untuk mengambil nilai pertama
     * dari beberapa kemungkinan key pada hasil perhitungan.
     *
     * @param  array<string, mixed>  $result
     * @param  array<int, string>  $keys
     */
    private function firstResultValue(array $result, array $keys): mixed
    {
        foreach ($keys as $key) {
            if (Arr::has($result, $key)) {
                return Arr::get($result, $key);
            }
        }

        return null;
    }

    /**
     * Function ini digunakan untuk mengubah kode gangguan
     * menjadi ID gangguan yang tersimpan di database.
     */
    private function resolveDisorderId(mixed $disorder): mixed
    {
        $reference = $this->sanitizeDisorderReference($disorder);

        if ($reference === null || is_numeric($reference)) {
            return $reference;
        }

        foreach ([MentalDisorder::class, Disorder::class] as $modelClass) {
            if (class_exists($modelClass)) {
                $id = $modelClass::query()->where('code', $reference)->value('id');

                if ($id !== null) {
                    return $id;
                }
            }
        }

        return null;
    }

    /**
     * Function ini digunakan untuk menyaring referensi hasil diagnosis
     * agar hanya satu kode gangguan valid yang boleh diproses ke foreign key.
     */
    private function sanitizeDisorderReference(mixed $disorder): int|string|null
    {
        if ($disorder === null || is_numeric($disorder)) {
            return $disorder;
        }

        $reference = trim((string) $disorder);

        if ($reference === '' || strcasecmp($reference, 'Theta') === 0) {
            return null;
        }

        if (str_contains($reference, ',') || str_contains($reference, '|')) {
            return null;
        }

        return $reference;
    }

    /**
     * Function ini digunakan untuk mengubah nilai keyakinan
     * menjadi skor belief dengan rentang 0 sampai 1.
     */
    private function confidenceScore(mixed $confidence): ?float
    {
        if (! is_numeric($confidence)) {
            return null;
        }

        $value = (float) $confidence;

        if ($value > 1 && $value <= 100) {
            return round($value / 100, 5);
        }

        return round(max(0, min(1, $value)), 5);
    }

    /**
     * Function ini digunakan untuk mengubah nilai keyakinan
     * menjadi persentase dengan rentang 0 sampai 100.
     */
    private function confidencePercentage(mixed $confidence): ?float
    {
        if (! is_numeric($confidence)) {
            return null;
        }

        $value = (float) $confidence;

        if ($value <= 1) {
            return round($value * 100, 2);
        }

        return round(max(0, min(100, $value)), 2);
    }

    /**
     * Function ini digunakan untuk menentukan label kepastian
     * berdasarkan nilai belief hasil diagnosis.
     */
    private function certaintyLabel(mixed $confidence): ?string
    {
        $score = $this->confidenceScore($confidence);

        if ($score === null) {
            return null;
        }

        return match (true) {
            $score >= 1.0 => 'Sangat Pasti',
            $score >= 0.75 => 'Pasti',
            $score >= 0.50 => 'Cukup Pasti',
            default => 'Kurang Pasti',
        };
    }

    /**
     * Function ini digunakan untuk menggabungkan data tambahan pasien
     * menjadi catatan konsultasi yang ringkas.
     *
     * @param  array<string, mixed>  $patient
     */
    private function patientNotes(array $patient): ?string
    {
        $notes = collect([
            'Sekolah' => $patient['school'] ?? null,
            'Orang tua/wali' => $patient['parent_guardian'] ?? null,
            'Pemicu stres' => $this->familyStressorLabel($patient['family_stressor'] ?? null),
            'Catatan' => $patient['notes'] ?? null,
        ])
            ->filter(fn (mixed $value): bool => filled($value))
            ->map(fn (mixed $value, string $label): string => "{$label}: {$value}")
            ->values()
            ->implode(PHP_EOL);

        return $notes !== '' ? $notes : null;
    }

    /**
     * Function ini digunakan untuk mengubah kode pemicu stres keluarga
     * menjadi teks yang mudah dibaca.
     */
    private function familyStressorLabel(mixed $familyStressor): ?string
    {
        if ($familyStressor === null) {
            return null;
        }

        return match ((string) $familyStressor) {
            'konflik' => 'Konflik keluarga',
            'komunikasi' => 'Komunikasi kurang baik',
            'ekonomi' => 'Tekanan ekonomi keluarga',
            'pengasuhan' => 'Pola asuh menekan',
            default => (string) $familyStressor,
        };
    }

    /**
     * Function ini digunakan untuk membuat kode konsultasi baru
     * yang unik berdasarkan tanggal dan karakter acak.
     */
    private function newConsultationCode(): string
    {
        return 'KSL-'.now()->format('Ymd').'-'.Str::upper(Str::random(8));
    }

    /**
     * Function ini digunakan untuk mencari kolom pertama
     * yang tersedia pada tabel model tertentu.
     *
     * @param  array<int, string>  $columns
     */
    private function firstExistingColumn(string $modelClass, array $columns): ?string
    {
        foreach ($columns as $column) {
            if ($this->hasColumn($modelClass, $column)) {
                return $column;
            }
        }

        return null;
    }

    /**
     * Function ini digunakan untuk mengecek apakah suatu kolom
     * tersedia pada tabel model yang diberikan.
     */
    private function hasColumn(string $modelClass, string $column): bool
    {
        $model = new $modelClass;
        $table = $model->getTable();

        if (! array_key_exists($table, $this->columnCache)) {
            $this->columnCache[$table] = Schema::getColumnListing($table);
        }

        return in_array($column, $this->columnCache[$table], true);
    }

    /**
     * Function ini digunakan untuk menyiapkan nilai sebelum disimpan,
     * terutama saat nilai array perlu diubah menjadi JSON.
     */
    private function valueForStorage(string $modelClass, string $column, mixed $value): mixed
    {
        if (! is_array($value)) {
            return $value;
        }

        $model = new $modelClass;

        if ($model->hasCast($column, ['array', 'json', 'object', 'collection'])) {
            return $value;
        }

        return json_encode($value);
    }

    /**
     * Function ini digunakan untuk memuat relasi konsultasi yang dikenal
     * agar halaman hasil dapat menampilkan data lengkap.
     */
    private function loadKnownRelations(Consultation $consultation): void
    {
        $relations = collect(['symptoms', 'disorder', 'detectedMentalDisorder', 'results', 'consultationSymptoms'])
            ->filter(fn (string $relation): bool => method_exists($consultation, $relation))
            ->all();

        if ($relations !== []) {
            $consultation->loadMissing($relations);
        }
    }
}
