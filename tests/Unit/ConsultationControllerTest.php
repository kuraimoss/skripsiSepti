<?php

namespace Tests\Unit;

use App\Http\Controllers\ConsultationController;
use PHPUnit\Framework\TestCase;

class ConsultationControllerTest extends TestCase
{
    /**
     * Function ini digunakan untuk memastikan referensi hasil diagnosis
     * yang bukan satu kode gangguan valid tidak diteruskan ke foreign key.
     */
    public function test_sanitize_disorder_reference_rejects_theta_and_composite_codes(): void
    {
        $controller = new ConsultationController;
        $sanitizeDisorderReference = \Closure::bind(
            fn (mixed $value): int|string|null => $this->sanitizeDisorderReference($value),
            $controller,
            $controller,
        );

        $this->assertNull($sanitizeDisorderReference('Theta'));
        $this->assertNull($sanitizeDisorderReference(' theta '));
        $this->assertNull($sanitizeDisorderReference('P01,P02'));
        $this->assertNull($sanitizeDisorderReference('P01|P02'));
        $this->assertSame('P01', $sanitizeDisorderReference(' P01 '));
        $this->assertSame(7, $sanitizeDisorderReference(7));
    }
}
