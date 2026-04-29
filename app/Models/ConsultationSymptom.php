<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['consultation_id', 'symptom_id', 'belief', 'selected', 'sort_order'])]
class ConsultationSymptom extends Model
{
    /**
     * Function ini digunakan untuk menentukan tipe data otomatis
     * pada atribut gejala konsultasi.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'belief' => 'float',
            'selected' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Function ini digunakan untuk menghubungkan data gejala terpilih
     * dengan konsultasi asalnya.
     */
    public function consultation(): BelongsTo
    {
        return $this->belongsTo(Consultation::class);
    }

    /**
     * Function ini digunakan untuk mengambil detail gejala
     * yang dipilih pada konsultasi.
     */
    public function symptom(): BelongsTo
    {
        return $this->belongsTo(Symptom::class);
    }
}
