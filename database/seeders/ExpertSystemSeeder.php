<?php

namespace Database\Seeders;

use App\Models\KnowledgeRule;
use App\Models\MentalDisorder;
use App\Models\Symptom;
use Illuminate\Database\Seeder;

class ExpertSystemSeeder extends Seeder
{
    /**
     * Function ini digunakan untuk mengisi data awal sistem pakar
     * berupa gangguan, gejala, dan aturan basis pengetahuan.
     */
    public function run(): void
    {
        $disorders = collect([
            [
                'code' => 'P01',
                'name' => 'Depresi',
                'scientific_name' => 'Depressive disorder',
                'solution' => 'Segera hubungi psikolog.',
            ],
            [
                'code' => 'P02',
                'name' => 'Gangguan kecemasan',
                'scientific_name' => 'Anxiety',
                'solution' => 'Melakukan terapi seperti cognitive behavioral therapy (CBT).',
            ],
        ])->mapWithKeys(function (array $data): array {
            $disorder = MentalDisorder::updateOrCreate(
                ['code' => $data['code']],
                [
                    'name' => $data['name'],
                    'scientific_name' => $data['scientific_name'],
                    'solution' => $data['solution'],
                ],
            );

            return [$disorder->code => $disorder];
        });

        MentalDisorder::query()->where('code', 'P03')->delete();

        $symptoms = collect([
            ['code' => 'G01', 'name' => 'Tindakan ingin bunuh diri', 'belief' => 0.8],
            ['code' => 'G02', 'name' => 'Malas berkomunikasi', 'belief' => 0.3],
            ['code' => 'G03', 'name' => 'Menarik diri dari keluarga', 'belief' => 0.6],
            ['code' => 'G04', 'name' => 'Mudah marah', 'belief' => 0.5],
            ['code' => 'G05', 'name' => 'Sulit berpikir jernih', 'belief' => 0.5],
            ['code' => 'G06', 'name' => 'Tidak memiliki nafsu makan', 'belief' => 0.3],
            ['code' => 'G07', 'name' => 'Aktivitas terganggu', 'belief' => 0.3],
            ['code' => 'G08', 'name' => 'Overtinhking', 'belief' => 0.5],
            ['code' => 'G09', 'name' => 'Merasa cemas berlebih', 'belief' => 0.4],
            ['code' => 'G10', 'name' => 'Tidak memiliki rasa percaya diri', 'belief' => 0.3],
            ['code' => 'G11', 'name' => 'Sulit tidur', 'belief' => 0.4],
            ['code' => 'G12', 'name' => 'Mudah takut', 'belief' => 0.2],
            ['code' => 'G13', 'name' => 'Sering menderita sakit kepala', 'belief' => 0.3],
            ['code' => 'G14', 'name' => 'Sedih berkepanjangan', 'belief' => 0.4],
            ['code' => 'G15', 'name' => 'Merasa tidak bahagia', 'belief' => 0.5],
        ])->mapWithKeys(function (array $data): array {
            $symptom = Symptom::updateOrCreate(
                ['code' => $data['code']],
                [
                    'name' => $data['name'],
                    'belief' => $data['belief'],
                ],
            );

            return [$symptom->code => $symptom];
        });

        $rules = [
            'R01' => [
                'disorder' => 'P01',
                'symptoms' => ['G01', 'G02', 'G03', 'G04', 'G05', 'G06', 'G07', 'G08'],
            ],
            'R02' => [
                'disorder' => 'P02',
                'symptoms' => ['G08', 'G09', 'G10', 'G11', 'G12', 'G13', 'G14', 'G15'],
            ],
        ];

        foreach ($rules as $ruleCode => $rule) {
            foreach ($rule['symptoms'] as $symptomCode) {
                $symptom = $symptoms->get($symptomCode);

                KnowledgeRule::updateOrCreate(
                    [
                        'mental_disorder_id' => $disorders->get($rule['disorder'])->id,
                        'symptom_id' => $symptom->id,
                    ],
                    [
                        'rule_code' => $ruleCode,
                        'belief' => $symptom->belief,
                    ],
                );
            }
        }
    }
}
