<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['code', 'name', 'belief', 'description'])]
class Symptom extends Model
{
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'belief' => 'decimal:4',
        ];
    }

    public function knowledgeRules(): HasMany
    {
        return $this->hasMany(KnowledgeRule::class);
    }

    public function mentalDisorders(): BelongsToMany
    {
        return $this->belongsToMany(MentalDisorder::class, 'knowledge_rules')
            ->withPivot(['rule_code', 'belief'])
            ->withTimestamps();
    }

    public function consultationSymptoms(): HasMany
    {
        return $this->hasMany(ConsultationSymptom::class);
    }

    public function consultations(): BelongsToMany
    {
        return $this->belongsToMany(Consultation::class, 'consultation_symptoms')
            ->withPivot(['belief', 'selected', 'sort_order'])
            ->withTimestamps();
    }
}
