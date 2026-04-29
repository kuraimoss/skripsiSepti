<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
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
])]
class Consultation extends Model
{
    /**
     * Get the attributes that should be cast.
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function detectedMentalDisorder(): BelongsTo
    {
        return $this->belongsTo(MentalDisorder::class, 'detected_mental_disorder_id');
    }

    public function disorder(): BelongsTo
    {
        return $this->detectedMentalDisorder();
    }

    public function consultationSymptoms(): HasMany
    {
        return $this->hasMany(ConsultationSymptom::class);
    }

    public function symptoms(): BelongsToMany
    {
        return $this->belongsToMany(Symptom::class, 'consultation_symptoms')
            ->withPivot(['belief', 'selected', 'sort_order'])
            ->withTimestamps();
    }

    public function results(): HasMany
    {
        return $this->hasMany(ConsultationResult::class);
    }

    public function getPatientNameAttribute(): ?string
    {
        return $this->respondent_name;
    }

    public function getAgeAttribute(): ?int
    {
        return $this->respondent_age;
    }

    public function getGenderAttribute(): ?string
    {
        return $this->respondent_gender;
    }

    public function getAddressAttribute(): ?string
    {
        return $this->respondent_address ?? $this->noteValue('Alamat');
    }

    public function getPhoneAttribute(): ?string
    {
        return $this->respondent_phone ?? $this->noteValue('Telepon');
    }

    public function getSchoolAttribute(): ?string
    {
        return $this->noteValue('Sekolah');
    }

    public function getParentGuardianAttribute(): ?string
    {
        return $this->noteValue('Orang tua/wali');
    }

    public function getFamilyStressorAttribute(): ?string
    {
        return $this->noteValue('Pemicu stres');
    }

    public function getAdditionalNotesAttribute(): ?string
    {
        return $this->noteValue('Catatan');
    }

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
