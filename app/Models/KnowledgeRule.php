<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KnowledgeRule extends Model
{
    /**
     * @var array<int, string>
     */
    protected $fillable = ['rule_code', 'mental_disorder_id', 'symptom_id', 'belief'];

    /**
     * Function ini digunakan untuk menentukan tipe data otomatis
     * pada atribut aturan basis pengetahuan.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'belief' => 'float',
        ];
    }

    /**
     * Function ini digunakan untuk mengambil gangguan
     * yang terhubung dengan aturan basis pengetahuan.
     */
    public function mentalDisorder(): BelongsTo
    {
        return $this->belongsTo(MentalDisorder::class);
    }

    /**
     * Function ini digunakan sebagai alias relasi
     * menuju gangguan pada aturan basis pengetahuan.
     */
    public function disorder(): BelongsTo
    {
        return $this->mentalDisorder();
    }

    /**
     * Function ini digunakan untuk mengambil gejala
     * yang terhubung dengan aturan basis pengetahuan.
     */
    public function symptom(): BelongsTo
    {
        return $this->belongsTo(Symptom::class);
    }
}
