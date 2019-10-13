<?php

namespace League\CommonMark\Tests\Unit\Util;

use League\CommonMark\Util\Html5EntityDecoder;
use PHPUnit\Framework\TestCase;

class Html5EntityDecoderTest extends TestCase
{
    public function testEntityToChar()
    {
        $this->assertEquals('©', Html5EntityDecoder::decodeEntity('&copy;'));
        $this->assertEquals('&copy', Html5EntityDecoder::decodeEntity('&copy'));
        $this->assertEquals('&MadeUpEntity;', Html5EntityDecoder::decodeEntity('&MadeUpEntity;'));
        $this->assertEquals('#', Html5EntityDecoder::decodeEntity('&#35;'));
        $this->assertEquals('Æ', Html5EntityDecoder::decodeEntity('&AElig;'));
        $this->assertEquals('Ď', Html5EntityDecoder::decodeEntity('&Dcaron;'));
    }
}
