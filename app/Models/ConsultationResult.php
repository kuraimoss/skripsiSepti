<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['consultation_id', 'mental_disorder_id', 'belief', 'percentage', 'rank', 'is_selected'])]
class ConsultationResult extends Model
{
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'belief' => 'decimal:5',
            'percentage' => 'decimal:2',
            'rank' => 'integer',
            'is_selected' => 'boolean',
        ];
    }

    public function consultation(): BelongsTo
    {
        return $this->belongsTo(Consultation::class);
    }

    public function mentalDisorder(): BelongsTo
    {
        return $this->belongsTo(MentalDisorder::class);
    }

    public function disorder(): BelongsTo
    {
        return $this->mentalDisorder();
    }
}
