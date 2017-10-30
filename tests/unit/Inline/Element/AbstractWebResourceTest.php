<?php

namespace League\CommonMark\Tests\Unit\Inline\Element;

use League\CommonMark\Inline\Element\AbstractWebResource;
use PHPUnit\Framework\TestCase;

class AbstractWebResourceTest extends TestCase
{
    /**
     * Tests the URL constructor parameter and getUrl() method
     */
    public function testConstructorAndGetUrl()
    {
        $url = 'https://www.example.com/foo';

        /** @var AbstractWebResource $element */
        $element = $this->getMockBuilder('League\\CommonMark\\Inline\\Element\\AbstractWebResource')
            ->setConstructorArgs([$url])
            ->getMockForAbstractClass();

        $this->assertEquals($url, $element->getUrl());
    }

    /**
     * Tests the setUrl() method
     */
    public function testSetUrl()
    {
        $url1 = 'https://www.example.com/foo';
        $url2 = 'https://www.example.com/bar';

        /** @var AbstractWebResource $element */
        $element = $this->getMockBuilder('League\\CommonMark\\Inline\\Element\\AbstractWebResource')
            ->setConstructorArgs([$url1])
            ->getMockForAbstractClass();

        $element->setUrl($url2);

        $this->assertEquals($url2, $element->getUrl());
    }
}
