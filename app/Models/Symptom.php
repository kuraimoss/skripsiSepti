<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Symptom extends Model
{
    /**
     * @var array<int, string>
     */
    protected $fillable = ['code', 'name', 'belief', 'description'];

    /**
     * Function ini digunakan untuk menentukan tipe data otomatis
     * pada atribut gejala.
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
     * Function ini digunakan untuk mengambil aturan basis pengetahuan
     * yang memakai gejala ini.
     */
    public function knowledgeRules(): HasMany
    {
        return $this->hasMany(KnowledgeRule::class);
    }

    /**
     * Function ini digunakan untuk mengambil daftar gangguan
     * yang berhubungan dengan gejala ini.
     */
    public function mentalDisorders(): BelongsToMany
    {
        return $this->belongsToMany(MentalDisorder::class, 'knowledge_rules')
            ->withPivot(['rule_code', 'belief'])
            ->withTimestamps();
    }

    /**
     * Function ini digunakan untuk mengambil detail konsultasi
     * yang pernah memilih gejala ini.
     */
    public function consultationSymptoms(): HasMany
    {
        return $this->hasMany(ConsultationSymptom::class);
    }

    /**
     * Function ini digunakan untuk mengambil daftar konsultasi
     * yang pernah memilih gejala ini.
     */
    public function consultations(): BelongsToMany
    {
        return $this->belongsToMany(Consultation::class, 'consultation_symptoms')
            ->withPivot(['belief', 'selected', 'sort_order'])
            ->withTimestamps();
    }
}
