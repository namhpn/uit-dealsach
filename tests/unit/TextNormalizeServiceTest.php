<?php

namespace Tests\Unit;

use App\Services\TextNormalizeService;
use CodeIgniter\Test\CIUnitTestCase;

class TextNormalizeServiceTest extends CIUnitTestCase
{
    public function testNormalizesVietnameseText(): void
    {
        $service = new TextNormalizeService();

        $this->assertSame('dac nhan tam', $service->normalize(' Đắc Nhân Tâm '));
        $this->assertSame('nha gia kim', $service->normalize('Nhà Giả Kim'));
        $this->assertSame('nguyen nhat anh', $service->normalize('Nguyễn Nhật Ánh'));
    }
}
