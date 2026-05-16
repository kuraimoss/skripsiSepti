<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Consultation extends Model
{
    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'user_id',
        'respondent_name',
        'respondent_age',
        'respondent_gender',
        'respondent_address',
        'respondent_phone',
        'started_at',
        'completed_at',
        'detected_mental_disorder_id',
        'confidence_score',
        'confidence_percentage',
        'certainty_label',
        'mass_values',
        'notes',
    ];

    /**
     * Function ini digunakan untuk menentukan tipe data otomatis
     * pada atribut konsultasi saat dibaca dari database.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'respondent_age' => 'integer',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'confidence_score' => 'float',
            'confidence_percentage' => 'float',
            'mass_values' => 'array',
        ];
    }

    /**
     * Function ini digunakan untuk menghubungkan konsultasi
     * dengan user yang membuat atau memiliki data tersebut.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Function ini digunakan untuk mengambil gangguan utama
     * yang terdeteksi pada konsultasi.
     */
    public function detectedMentalDisorder(): BelongsTo
    {
        return $this->belongsTo(MentalDisorder::class, 'detected_mental_disorder_id');
    }

    /**
     * Function ini digunakan sebagai alias relasi
     * menuju gangguan utama yang terdeteksi.
     */
    public function disorder(): BelongsTo
    {
        return $this->detectedMentalDisorder();
    }

    /**
     * Function ini digunakan untuk mengambil detail gejala
     * yang tersimpan pada satu konsultasi.
     */
    public function consultationSymptoms(): HasMany
    {
        return $this->hasMany(ConsultationSymptom::class);
    }

    /**
     * Function ini digunakan untuk mengambil daftar gejala
     * yang dipilih pada konsultasi melalui tabel pivot.
     */
    public function symptoms(): BelongsToMany
    {
        return $this->belongsToMany(Symptom::class, 'consultation_symptoms')
            ->withPivot(['belief', 'selected', 'sort_order'])
            ->withTimestamps();
    }

    /**
     * Function ini digunakan untuk mengambil daftar hasil diagnosis
     * yang dihasilkan pada konsultasi.
     */
    public function results(): HasMany
    {
        return $this->hasMany(ConsultationResult::class);
    }

    /**
     * Function ini digunakan untuk membaca nama pasien
     * dari atribut respondent_name.
     */
    public function getPatientNameAttribute(): ?string
    {
        return $this->respondent_name;
    }

    /**
     * Function ini digunakan untuk membaca umur pasien
     * dari atribut respondent_age.
     */
    public function getAgeAttribute(): ?int
    {
        return $this->respondent_age;
    }

    /**
     * Function ini digunakan untuk membaca jenis kelamin pasien
     * dari atribut respondent_gender.
     */
    public function getGenderAttribute(): ?string
    {
        return $this->respondent_gender;
    }

    /**
     * Function ini digunakan untuk membaca alamat pasien
     * dari kolom utama atau catatan konsultasi.
     */
    public function getAddressAttribute(): ?string
    {
        return $this->respondent_address ?? $this->noteValue('Alamat');
    }

    /**
     * Function ini digunakan untuk membaca nomor telepon pasien
     * dari kolom utama atau catatan konsultasi.
     */
    public function getPhoneAttribute(): ?string
    {
        return $this->respondent_phone ?? $this->noteValue('Telepon');
    }

    /**
     * Function ini digunakan untuk membaca nama sekolah
     * dari catatan tambahan konsultasi.
     */
    public function getSchoolAttribute(): ?string
    {
        return $this->noteValue('Sekolah');
    }

    /**
     * Function ini digunakan untuk membaca nama orang tua atau wali
     * dari catatan tambahan konsultasi.
     */
    public function getParentGuardianAttribute(): ?string
    {
        return $this->noteValue('Orang tua/wali');
    }

    /**
     * Function ini digunakan untuk membaca pemicu stres keluarga
     * dari catatan tambahan konsultasi.
     */
    public function getFamilyStressorAttribute(): ?string
    {
        return $this->noteValue('Pemicu stres');
    }

    /**
     * Function ini digunakan untuk membaca catatan tambahan
     * dari data konsultasi.
     */
    public function getAdditionalNotesAttribute(): ?string
    {
        return $this->noteValue('Catatan');
    }

    /**
     * Function ini digunakan untuk mengambil hasil diagnosis utama
     * dari daftar hasil konsultasi.
     */
    public function getPrimaryResultAttribute(): ?ConsultationResult
    {
        if ($this->relationLoaded('results')) {
            return $this->results
                ->sortBy(fn (ConsultationResult $result): int => $result->rank ?? PHP_INT_MAX)
                ->firstWhere('is_selected', true)
                ?? $this->results->sortBy('rank')->first();
        }

        return $this->results()
            ->with('mentalDisorder')
            ->orderByDesc('is_selected')
            ->orderBy('rank')
            ->first();
    }

    /**
     * Function ini digunakan untuk menampilkan nama hasil utama
     * dari relasi hasil atau snapshot perhitungan Dempster-Shafer.
     */
    public function getDisplayResultNameAttribute(): string
    {
        $primaryResultName = $this->firstPrimaryResultValue([
            'disorder.name',
            'mentalDisorder.name',
            'name',
        ]);

        if (filled($primaryResultName)) {
            return (string) $primaryResultName;
        }

        $snapshotResultName = $this->firstSnapshotValue([
            'result.name',
            'diagnosis.name',
            'best_disorder.name',
            'disorder.name',
            'disorder',
        ]);

        return filled($snapshotResultName) ? (string) $snapshotResultName : 'Hasil belum tersedia';
    }

    /**
     * Function ini digunakan untuk menampilkan kode hasil utama
     * dari relasi hasil atau snapshot perhitungan Dempster-Shafer.
     */
    public function getDisplayResultCodeAttribute(): ?string
    {
        $primaryResultCode = $this->firstPrimaryResultValue([
            'disorder.code',
            'mentalDisorder.code',
            'code',
        ]);

        if (filled($primaryResultCode)) {
            return (string) $primaryResultCode;
        }

        $snapshotResultCode = $this->firstSnapshotValue([
            'result.code',
            'diagnosis.code',
            'best_disorder.code',
            'disorder.code',
            'code',
        ]);

        return filled($snapshotResultCode) ? (string) $snapshotResultCode : null;
    }

    /**
     * Function ini digunakan untuk menampilkan skor keyakinan
     * dengan rentang 0 sampai 1 dari hasil konsultasi.
     */
    public function getDisplayConfidenceScoreAttribute(): float
    {
        $primaryScore = $this->normalizeConfidenceScore($this->firstPrimaryResultValue([
            'belief',
            'confidence',
            'score',
            'percentage',
        ]));

        if ($primaryScore !== null) {
            return $primaryScore;
        }

        $storedScore = $this->normalizeConfidenceScore($this->confidence_score);

        if ($storedScore !== null) {
            return $storedScore;
        }

        $snapshotScore = $this->normalizeConfidenceScore($this->firstSnapshotValue([
            'belief',
            'confidence',
            'score',
            'percentage',
            'result.belief',
            'result.confidence',
            'diagnosis.belief',
            'diagnosis.confidence',
            'best_disorder.belief',
            'best_disorder.confidence',
        ]));

        return $snapshotScore ?? 0.0;
    }

    /**
     * Function ini digunakan untuk menampilkan persentase keyakinan
     * dengan rentang 0 sampai 100 dari hasil konsultasi.
     */
    public function getDisplayConfidencePercentageAttribute(): float
    {
        $primaryPercentage = $this->normalizeConfidencePercentage($this->firstPrimaryResultValue([
            'percentage',
            'belief',
            'confidence',
            'score',
        ]));

        if ($primaryPercentage !== null) {
            return $primaryPercentage;
        }

        $storedPercentage = $this->normalizeConfidencePercentage($this->confidence_percentage);

        if ($storedPercentage !== null) {
            return $storedPercentage;
        }

        $snapshotPercentage = $this->normalizeConfidencePercentage($this->firstSnapshotValue([
            'percentage',
            'belief',
            'confidence',
            'score',
            'result.percentage',
            'result.belief',
            'result.confidence',
            'diagnosis.percentage',
            'diagnosis.belief',
            'diagnosis.confidence',
            'best_disorder.percentage',
            'best_disorder.belief',
            'best_disorder.confidence',
        ]));

        return $snapshotPercentage ?? round($this->display_confidence_score * 100, 2);
    }

    /**
     * Function ini digunakan untuk menampilkan label kepastian
     * berdasarkan data tersimpan atau skor keyakinan hasil konsultasi.
     */
    public function getDisplayCertaintyLabelAttribute(): string
    {
        if (filled($this->certainty_label)) {
            return (string) $this->certainty_label;
        }

        return $this->certaintyLabelFromScore($this->display_confidence_score);
    }

    /**
     * Function ini digunakan untuk mengambil nilai tertentu
     * dari catatan konsultasi berdasarkan label teks.
     */
    private function noteValue(string $label): ?string
    {
        foreach (preg_split('/\R/', (string) $this->notes) ?: [] as $line) {
            [$lineLabel, $value] = array_pad(explode(':', $line, 2), 2, null);

            if (strcasecmp(trim((string) $lineLabel), $label) === 0 && filled($value)) {
                return trim((string) $value);
            }
        }

        return null;
    }

    /**
     * Function ini digunakan untuk mengambil nilai pertama
     * dari hasil utama yang tersimpan pada relasi hasil konsultasi.
     *
     * @param  array<int, string>  $paths
     */
    private function firstPrimaryResultValue(array $paths): mixed
    {
        $primaryResult = $this->primary_result;

        foreach ($paths as $path) {
            $value = data_get($primaryResult, $path);

            if ($value !== null) {
                return $value;
            }
        }

        return null;
    }

    /**
     * Function ini digunakan untuk mengambil nilai pertama
     * dari snapshot hasil perhitungan Dempster-Shafer.
     *
     * @param  array<int, string>  $paths
     */
    private function firstSnapshotValue(array $paths): mixed
    {
        $snapshot = $this->mass_values ?? [];

        foreach ($paths as $path) {
            $value = data_get($snapshot, $path);

            if ($value !== null) {
                return $value;
            }
        }

        return null;
    }

    /**
     * Function ini digunakan untuk menormalkan nilai keyakinan
     * menjadi skor dengan rentang 0 sampai 1.
     */
    private function normalizeConfidenceScore(mixed $confidence): ?float
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
     * Function ini digunakan untuk menormalkan nilai keyakinan
     * menjadi persentase dengan rentang 0 sampai 100.
     */
    private function normalizeConfidencePercentage(mixed $confidence): ?float
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
     * berdasarkan rentang nilai pada dokumen skripsi.
     */
    private function certaintyLabelFromScore(float $score): string
    {
        return match (true) {
            $score >= 1.0 => 'Sangat Pasti',
            $score >= 0.75 => 'Pasti',
            $score >= 0.50 => 'Cukup Pasti',
            default => 'Kurang Pasti',
        };
    }
}
