<?php

namespace Tests\Unit;

use App\Services\DempsterShaferService;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

class DempsterShaferServiceTest extends TestCase
{
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

    public function test_diagnose_accepts_collection_and_uses_deterministic_knowledge_base_order(): void
    {
        $service = new DempsterShaferService;

        $result = $service->diagnose(new Collection(['G11', 'G08', 'G03', 'G01']));

        $this->assertSame(['G01', 'G03', 'G08', 'G11'], array_column($result['selected_symptoms'], 'code'));
        $this->assertSame('P02', $result['result']['code']);
        $this->assertEqualsWithDelta(0.976, $result['belief'], 0.000001);
    }

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
