<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['rule_code', 'mental_disorder_id', 'symptom_id', 'belief'])]
class KnowledgeRule extends Model
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

    public function mentalDisorder(): BelongsTo
    {
        return $this->belongsTo(MentalDisorder::class);
    }

    public function disorder(): BelongsTo
    {
        return $this->mentalDisorder();
    }

    public function symptom(): BelongsTo
    {
        return $this->belongsTo(Symptom::class);
    }
}
