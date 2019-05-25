<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (https://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
        $element = $this->getMockBuilder(AbstractWebResource::class)
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
        $element = $this->getMockBuilder(AbstractWebResource::class)
            ->setConstructorArgs([$url1])
            ->getMockForAbstractClass();

        $element->setUrl($url2);

        $this->assertEquals($url2, $element->getUrl());
    }

    public function testIsContainer()
    {
        /** @var AbstractWebResource $element */
        $element = $this->getMockBuilder(AbstractWebResource::class)
            ->setConstructorArgs(['https://www.example.com'])
            ->getMockForAbstractClass();

        $this->assertTrue($element->isContainer());
    }
}
