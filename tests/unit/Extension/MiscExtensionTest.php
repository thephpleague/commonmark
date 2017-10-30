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

namespace League\CommonMark\Tests\Unit\Extension;

use League\CommonMark\Extension\MiscExtension;
use PHPUnit\Framework\TestCase;

class MiscExtensionTest extends TestCase
{
    public function testBlockParsers()
    {
        $extension = new MiscExtension();
        $this->assertEquals([], $extension->getBlockParsers());

        $parser = $this->getMockForAbstractClass('League\CommonMark\Block\Parser\BlockParserInterface');
        $extension->addBlockParser($parser);
        $this->assertEquals([$parser], $extension->getBlockParsers());
    }

    public function testBlockRenderers()
    {
        $extension = new MiscExtension();
        $this->assertEquals([], $extension->getBlockRenderers());

        $renderer = $this->getMockForAbstractClass('League\CommonMark\Block\Renderer\BlockRendererInterface');
        $extension->addBlockRenderer('foo', $renderer);
        $this->assertEquals(['foo' => $renderer], $extension->getBlockRenderers());

        $renderer2 = $this->getMockForAbstractClass('League\CommonMark\Block\Renderer\BlockRendererInterface');
        $extension->addBlockRenderer('foo', $renderer2);
        $this->assertEquals(['foo' => $renderer2], $extension->getBlockRenderers());
    }

    public function testInlineParsers()
    {
        $extension = new MiscExtension();
        $this->assertEquals([], $extension->getInlineParsers());

        $parser = $this->getMockForAbstractClass('League\CommonMark\Inline\Parser\InlineParserInterface');
        $extension->addInlineParser($parser);
        $this->assertEquals([$parser], $extension->getInlineParsers());
    }

    public function testInlineProcessors()
    {
        $extension = new MiscExtension();
        $this->assertEquals([], $extension->getInlineProcessors());

        $parser = $this->getMockForAbstractClass('League\CommonMark\Inline\Processor\InlineProcessorInterface');
        $extension->addInlineProcessor($parser);
        $this->assertEquals([$parser], $extension->getInlineProcessors());
    }

    public function testInlineRenderers()
    {
        $extension = new MiscExtension();
        $this->assertEquals([], $extension->getInlineRenderers());

        $renderer = $this->getMockForAbstractClass('League\CommonMark\Inline\Renderer\InlineRendererInterface');
        $extension->addInlineRenderer('foo', $renderer);
        $this->assertEquals(['foo' => $renderer], $extension->getInlineRenderers());

        $renderer2 = $this->getMockForAbstractClass('League\CommonMark\Inline\Renderer\InlineRendererInterface');
        $extension->addInlineRenderer('foo', $renderer2);
        $this->assertEquals(['foo' => $renderer2], $extension->getInlineRenderers());
    }

    public function testDocumentProcessors()
    {
        $extension = new MiscExtension();
        $this->assertEquals([], $extension->getDocumentProcessors());

        $processor = $this->getMockForAbstractClass('League\CommonMark\DocumentProcessorInterface');
        $extension->addDocumentProcessor($processor);
        $this->assertEquals([$processor], $extension->getDocumentProcessors());
    }
}
