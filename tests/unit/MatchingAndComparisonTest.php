<?php

namespace Tests\Unit;

use App\Services\ComparisonService;
use App\Services\MatchingService;
use App\Services\TextNormalizeService;
use CodeIgniter\Test\CIUnitTestCase;

class MatchingAndComparisonTest extends CIUnitTestCase
{
    public function testTitleAuthorScoreHandlesVietnameseDiacritics(): void
    {
        $service = new MatchingService(null, new TextNormalizeService());

        $score = $service->scoreTitleAuthor('nha gia kim', 'paulo coelho', 'Nhà Giả Kim', 'Paulo Coelho');

        $this->assertGreaterThanOrEqual(0.95, $score);
    }

    public function testLowestAvailableIgnoresOutOfStockAndMissingPrice(): void
    {
        $service = new ComparisonService();
        $offers = $service->markLowestAvailable([
            ['id' => 1, 'in_stock' => false, 'current_effective_price' => 50000],
            ['id' => 2, 'in_stock' => true, 'current_effective_price' => null],
            ['id' => 3, 'in_stock' => true, 'current_effective_price' => 79000],
            ['id' => 4, 'in_stock' => true, 'current_effective_price' => 82000],
        ]);

        $this->assertFalse($offers[0]['is_lowest_available']);
        $this->assertFalse($offers[1]['is_lowest_available']);
        $this->assertTrue($offers[2]['is_lowest_available']);
        $this->assertFalse($offers[3]['is_lowest_available']);
    }
}
