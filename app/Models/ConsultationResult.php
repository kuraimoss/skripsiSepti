<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConsultationResult extends Model
{
    /**
     * @var array<int, string>
     */
    protected $fillable = ['consultation_id', 'mental_disorder_id', 'belief', 'percentage', 'rank', 'is_selected'];

    /**
     * Function ini digunakan untuk menentukan tipe data otomatis
     * pada atribut hasil konsultasi.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'belief' => 'float',
            'percentage' => 'float',
            'rank' => 'integer',
            'is_selected' => 'boolean',
        ];
    }

    /**
     * Function ini digunakan untuk menghubungkan hasil diagnosis
     * dengan data konsultasi asalnya.
     */
    public function consultation(): BelongsTo
    {
        return $this->belongsTo(Consultation::class);
    }

    /**
     * Function ini digunakan untuk mengambil gangguan
     * yang terkait dengan hasil diagnosis.
     */
    public function mentalDisorder(): BelongsTo
    {
        return $this->belongsTo(MentalDisorder::class);
    }

    /**
     * Function ini digunakan sebagai alias relasi
     * menuju gangguan pada hasil diagnosis.
     */
    public function disorder(): BelongsTo
    {
        return $this->mentalDisorder();
    }
}
