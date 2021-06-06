<?php

declare(strict_types=1);

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Tests\Unit\Xml;

use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Extension\SmartPunct\Quote;
use League\CommonMark\Xml\FallbackNodeXmlRenderer;
use PHPUnit\Framework\TestCase;

final class FallbackNodeXmlRendererTest extends TestCase
{
    public function testIt(): void
    {
        $renderer = new FallbackNodeXmlRenderer();

        $block = new Heading(3);
        $this->assertSame('custom_block_heading', $renderer->getXmlTagName($block));
        $this->assertSame(['level' => 3], $renderer->getXmlAttributes($block));

        $inline = new Quote('"');
        $this->assertSame('custom_inline_quote', $renderer->getXmlTagName($inline));
        $this->assertSame(['literal' => '"'], $renderer->getXmlAttributes($inline));
    }
}
