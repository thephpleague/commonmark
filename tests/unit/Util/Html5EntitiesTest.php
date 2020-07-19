<?php

namespace League\CommonMark\Tests\Unit\Util;

use League\CommonMark\Util\Html5Entities;
use PHPUnit\Framework\TestCase;

/**
 * @group legacy
 */
class Html5EntitiesTest extends TestCase
{
    public function testEntityToChar()
    {
        $this->assertEquals('©', Html5Entities::decodeEntity('&copy;'));
        $this->assertEquals('&copy', Html5Entities::decodeEntity('&copy'));
        $this->assertEquals('&MadeUpEntity;', Html5Entities::decodeEntity('&MadeUpEntity;'));
        $this->assertEquals('#', Html5Entities::decodeEntity('&#35;'));
        $this->assertEquals('Æ', Html5Entities::decodeEntity('&AElig;'));
        $this->assertEquals('Ď', Html5Entities::decodeEntity('&Dcaron;'));
    }

    public function testFromDecimal()
    {
        $this->assertEquals('A', Html5Entities::fromDecimal(65));
        $this->assertEquals('A', Html5Entities::fromDecimal('65'));

        $this->assertEquals('😄', Html5Entities::fromDecimal(128516));
        $this->assertEquals('😄', Html5Entities::fromDecimal('128516'));

        // Test for things which should return U+FFFD REPLACEMENT CHARACTER
        $this->assertEquals('�', Html5Entities::fromDecimal(null));
        $this->assertEquals('�', Html5Entities::fromDecimal(0));
        $this->assertEquals('�', Html5Entities::fromDecimal(0x30000));
    }

    public function testFromHex()
    {
        $this->assertEquals('A', Html5Entities::fromHex('41'));

        $this->assertEquals('😄', Html5Entities::fromHex('1f604'));

        // Test for things which should return U+FFFD REPLACEMENT CHARACTER
        $this->assertEquals('�', Html5Entities::fromHex(''));
        $this->assertEquals('�', Html5Entities::fromHex('fffd'));
        $this->assertEquals('�', Html5Entities::fromHex('ffffffff'));
    }
}
