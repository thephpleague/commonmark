<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (http://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Tests\Unit;

use League\CommonMark\Block\Element\BlockQuote;
use League\CommonMark\Block\Element\Document;
use League\CommonMark\Block\Element\Paragraph;
use League\CommonMark\ElementTraverser;
use League\CommonMark\Environment;
use League\CommonMark\Inline\Element\Emphasis;
use League\CommonMark\Inline\Element\Link;
use League\CommonMark\Inline\Element\Text;

class ElementTraverserTest extends \PHPUnit_Framework_TestCase
{
    public function testTraverse()
    {
        $document = new Document();
        $document->addChild($b1 = new BlockQuote());
        $document->addChild($p2 = new Paragraph());
        $document->addChild($p3 = new Paragraph());
        $b1->addChild($p4 = new Paragraph());
        $p2->setInlines([
            $i1 = new Link('http://example.org'),
            $i2 = new Text(),
        ]);
        $i3 = $i1->getChildren()->first();

        $visitor = $this->getMock('League\CommonMark\ElementVisitorInterface');

        $visitor->expects($this->at(1))->method('enterBlock')->with($document)->willReturn($document);
        $visitor->expects($this->at(2))->method('enterBlock')->with($b1)->willReturn($b1);
        $visitor->expects($this->at(3))->method('enterBlock')->with($p4)->willReturn($p4);
        $visitor->expects($this->at(4))->method('leaveBlock')->with($p4)->willReturn($p5 = new Paragraph());
        $visitor->expects($this->at(5))->method('leaveBlock')->with($b1)->willReturn($b1);
        $visitor->expects($this->at(6))->method('enterBlock')->with($p2)->willReturn($p2);
        $visitor->expects($this->at(7))->method('enterInline')->with($i1)->willReturn($i1);
        $visitor->expects($this->at(8))->method('enterInline')->with($i3)->willReturn($i3);
        $visitor->expects($this->at(9))->method('leaveInline')->with($i3)->willReturn($i4 = new Emphasis());
        $visitor->expects($this->at(10))->method('leaveInline')->with($i1)->willReturn($i1);
        $visitor->expects($this->at(11))->method('enterInline')->with($i2)->willReturn($i2);
        $visitor->expects($this->at(12))->method('leaveInline')->with($i2)->willReturn(false);
        $visitor->expects($this->at(13))->method('leaveBlock')->with($p2)->willReturn($p2);
        $visitor->expects($this->at(14))->method('enterBlock')->with($p3)->willReturn($p3);
        $visitor->expects($this->at(15))->method('leaveBlock')->with($p3)->willReturn(false);
        $visitor->expects($this->at(16))->method('leaveBlock')->with($document)->willReturn($document);

        $environment = new Environment();
        $environment->addElementVisitor($visitor);

        $traverser = new ElementTraverser($environment);

        $this->assertEquals($document, $traverser->traverseBlock($document));

        $children = $document->getChildren();
        $this->assertCount(2, $children);
        $this->assertSame($p5, $children[0]->getLastChild());
        $inlines = $children[1]->getInlines();
        $this->assertCount(1, $inlines);
        $this->assertSame([$i4], $inlines[0]->getChildren());
    }
}
