<?php

namespace Tests\Unit;

use App\Services\DempsterShaferService;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

class DempsterShaferServiceTest extends TestCase
{
    /**
     * Function ini digunakan untuk memastikan contoh perhitungan dokumen
     * menghasilkan belief, persentase, dan label kepastian yang benar.
     */
    public function test_document_example_returns_expected_belief_percentage_and_certainty(): void
    {
        $service = new DempsterShaferService;

        $result = $service->diagnose(['G01', 'G03', 'G08', 'G11']);

        $this->assertSame('P02', $result['result']['code']);
        $this->assertSame('Gangguan kecemasan (anxiety)', $result['disorder']);
        $this->assertEqualsWithDelta(0.976, $result['belief'], 0.000001);
        $this->assertSame(97.6, $result['percentage']);
        $this->assertSame('97.6%', $result['percentage_text']);
        $this->assertSame('Pasti', $result['certainty']);
        $this->assertSame('Pasti', $result['certainty_label']);
        $this->assertSame(['P02' => 0.976, 'Theta' => 0.024], $result['masses']);
        $this->assertCount(4, $result['steps']);
        $this->assertSame(['G01', 'G03', 'G08', 'G11'], array_column($result['selected_symptoms'], 'code'));
    }

    /**
     * Function ini digunakan untuk memastikan input collection tetap
     * diproses dengan urutan basis pengetahuan yang konsisten.
     */
    public function test_diagnose_accepts_collection_and_uses_deterministic_knowledge_base_order(): void
    {
        $service = new DempsterShaferService;

        $result = $service->diagnose(new Collection(['G11', 'G08', 'G03', 'G01']));

        $this->assertSame(['G01', 'G03', 'G08', 'G11'], array_column($result['selected_symptoms'], 'code'));
        $this->assertSame('P02', $result['result']['code']);
        $this->assertEqualsWithDelta(0.976, $result['belief'], 0.000001);
    }

    /**
     * Function ini digunakan untuk memastikan gejala yang dimiliki dua gangguan
     * tetap menghasilkan hipotesis gabungan sesuai basis pengetahuan skripsi.
     */
    public function test_shared_symptom_returns_composite_hypothesis_and_correct_certainty_label(): void
    {
        $service = new DempsterShaferService;

        $result = $service->diagnose(['G08']);

        $this->assertSame('P01,P02', $result['result']['code']);
        $this->assertSame('Depresi (depressive disorder) / Gangguan kecemasan (anxiety)', $result['result']['name']);
        $this->assertSame(0.5, $result['belief']);
        $this->assertSame(50.0, $result['percentage']);
        $this->assertSame('50%', $result['percentage_text']);
        $this->assertSame('Cukup Pasti', $result['certainty_label']);
        $this->assertSame(['P01,P02' => 0.5, 'Theta' => 0.5], $result['masses']);
    }

    /**
     * Function ini digunakan untuk memastikan input gejala kosong
     * menghasilkan hasil deteksi kosong yang aman.
     */
    public function test_empty_symptoms_return_empty_detection_result(): void
    {
        $service = new DempsterShaferService;

        $result = $service->diagnose([]);

        $this->assertNull($result['result']);
        $this->assertNull($result['disorder']);
        $this->assertSame(0.0, $result['belief']);
        $this->assertSame(0.0, $result['percentage']);
        $this->assertSame('Kurang Pasti', $result['certainty']);
        $this->assertSame([], $result['steps']);
        $this->assertSame([], $result['selected_symptoms']);
    }
}
