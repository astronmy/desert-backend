<?php

namespace Tests\Unit;

use App\Models\Guest;
use PHPUnit\Framework\TestCase;

class GuestDisplayNameTest extends TestCase
{
    public function test_it_normalizes_names_to_title_case(): void
    {
        $this->assertSame('Renzo', Guest::formatDisplayName('renzo'));
        $this->assertSame('Renzo', Guest::formatDisplayName('RENZO'));
        $this->assertSame('Renzo', Guest::formatDisplayName('ReNzO'));
        $this->assertSame('Ana María', Guest::formatDisplayName('ana maría'));
        $this->assertSame('', Guest::formatDisplayName('   '));
    }
}
