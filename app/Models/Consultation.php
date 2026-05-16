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
}
