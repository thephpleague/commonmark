<?php

declare(strict_types=1);

namespace League\CommonMark\Tests\Unit\Extension\Marker;

use League\CommonMark\Extension\Marker\Marker;
use PHPUnit\Framework\TestCase;

final class MarkerTest extends TestCase 
{
    public function testEmptyConstructor(): void 
    {
        $emphasis = new Marker();
        $this->assertSame('==', $emphasis->getOpeningDelimiter());
        $this->assertSame('==', $emphasis->getClosingDelimiter());
    }

    public function testConstructor(): void 
    {
        $emphasis = new Marker('===');
        $this->assertSame('===', $emphasis->getOpeningDelimiter());
        $this->assertSame('===', $emphasis->getClosingDelimiter());
    }
}