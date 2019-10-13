<?php

namespace League\CommonMark\Tests\Unit\Util;

use League\CommonMark\Util\Html5EntityDecoder;
use PHPUnit\Framework\TestCase;

class Html5EntityDecoderTest extends TestCase
{
    public function testEntityToChar()
    {
        $this->assertEquals('©', Html5EntityDecoder::decode('&copy;'));
        $this->assertEquals('&copy', Html5EntityDecoder::decode('&copy'));
        $this->assertEquals('&MadeUpEntity;', Html5EntityDecoder::decode('&MadeUpEntity;'));
        $this->assertEquals('#', Html5EntityDecoder::decode('&#35;'));
        $this->assertEquals('Æ', Html5EntityDecoder::decode('&AElig;'));
        $this->assertEquals('Ď', Html5EntityDecoder::decode('&Dcaron;'));
    }
}
