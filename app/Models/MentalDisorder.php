<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['code', 'name', 'scientific_name', 'description', 'solution'])]
class MentalDisorder extends Model
{
    public function knowledgeRules(): HasMany
    {
        return $this->hasMany(KnowledgeRule::class);
    }

    public function symptoms(): BelongsToMany
    {
        return $this->belongsToMany(Symptom::class, 'knowledge_rules')
            ->withPivot(['rule_code', 'belief'])
            ->withTimestamps();
    }

    public function detectedConsultations(): HasMany
    {
        return $this->hasMany(Consultation::class, 'detected_mental_disorder_id');
    }

    public function consultationResults(): HasMany
    {
        return $this->hasMany(ConsultationResult::class);
    }
}
