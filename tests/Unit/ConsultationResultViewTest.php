<?php

namespace Tests\Unit;

use App\Models\Consultation;
use App\Models\Symptom;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Collection;
use Illuminate\Support\ViewErrorBag;
use Tests\TestCase;

class ConsultationResultViewTest extends TestCase
{
    /**
     * Function ini digunakan untuk memastikan halaman hasil tetap menampilkan
     * snapshot Dempster-Shafer saat hasilnya berupa gabungan hipotesis.
     */
    public function test_result_page_uses_consultation_snapshot_when_no_single_disorder_result_exists(): void
    {
        View::share('errors', new ViewErrorBag);

        $consultation = new Consultation([
            'id' => 1,
            'respondent_name' => 'Kurai',
            'respondent_age' => 17,
            'respondent_gender' => 'laki-laki',
            'confidence_score' => 0.5,
            'confidence_percentage' => 50,
            'certainty_label' => 'Cukup Pasti',
            'mass_values' => [
                'result' => [
                    'code' => 'P01,P02',
                    'name' => 'Depresi / Gangguan kecemasan',
                ],
                'belief' => 0.5,
                'percentage' => 50,
            ],
        ]);

        $consultation->setRelation('results', new Collection);
        $consultation->setRelation('symptoms', new Collection([
            new Symptom(['name' => 'Overthinking']),
        ]));

        $html = view('consultation.show', [
            'consultation' => $consultation,
        ])->render();

        $this->assertStringContainsString('Depresi / Gangguan kecemasan', $html);
        $this->assertStringContainsString('50%', $html);
        $this->assertStringContainsString('Cukup Pasti', $html);
        $this->assertStringContainsString('Overthinking', $html);
        $this->assertStringNotContainsString('Hasil belum tersedia', $html);
    }
}
