<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MentalDisorder extends Model
{
    /**
     * @var array<int, string>
     */
    protected $fillable = ['code', 'name', 'scientific_name', 'description', 'solution'];

    /**
     * Function ini digunakan untuk mengambil aturan basis pengetahuan
     * yang dimiliki oleh satu gangguan.
     */
    public function knowledgeRules(): HasMany
    {
        return $this->hasMany(KnowledgeRule::class);
    }

    /**
     * Function ini digunakan untuk mengambil daftar gejala
     * yang berhubungan dengan satu gangguan.
     */
    public function symptoms(): BelongsToMany
    {
        return $this->belongsToMany(Symptom::class, 'knowledge_rules')
            ->withPivot(['rule_code', 'belief'])
            ->withTimestamps();
    }

    /**
     * Function ini digunakan untuk mengambil riwayat konsultasi
     * yang mendeteksi gangguan ini sebagai hasil utama.
     */
    public function detectedConsultations(): HasMany
    {
        return $this->hasMany(Consultation::class, 'detected_mental_disorder_id');
    }

    /**
     * Function ini digunakan untuk mengambil semua hasil konsultasi
     * yang berhubungan dengan gangguan ini.
     */
    public function consultationResults(): HasMany
    {
        return $this->hasMany(ConsultationResult::class);
    }
}
