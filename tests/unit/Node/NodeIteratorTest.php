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

namespace League\CommonMark\Tests\Unit\Node;

use League\CommonMark\Extension\CommonMark\Node\Inline\Emphasis;
use League\CommonMark\Node\Block\Document;
use League\CommonMark\Node\Block\Paragraph;
use League\CommonMark\Node\Inline\Text;
use PHPUnit\Framework\TestCase;

final class NodeIteratorTest extends TestCase
{
    public function testIterator(): void
    {
        $document = new Document();
        $document->appendChild($paragraph1 = new Paragraph());
        $paragraph1->appendChild($text1 = new Text());
        $paragraph1->appendChild($emphasis = new Emphasis('*'));
        $emphasis->appendChild($text2 = new Text());
        $document->appendChild($paragraph2 = new Paragraph());
        $paragraph2->appendChild($text3 = new Text());

        $iterator = $document->iterator();

        $expected = [
            0 => $document,
            1 => $paragraph1,
            2 => $text1,
            3 => $emphasis,
            4 => $text2,
            5 => $paragraph2,
            6 => $text3,
        ];

        $this->assertSame($expected, \iterator_to_array($iterator));
    }

    public function testSiblingChangesWhileIterating(): void
    {
        $document = new Document();
        $document->appendChild($paragraph1 = new Paragraph());
        $paragraph1->appendChild($text1 = new Text());
        $paragraph1->appendChild($emphasis = new Emphasis('*'));
        $emphasis->appendChild($text2 = new Text());
        $paragraph1->appendChild($text3 = new Text());

        $this->assertCount(6, \iterator_to_array($document->iterator()));

        $nodes = [];
        foreach ($document->iterator() as $node) {
            $nodes[] = $node;

            // While iterating, removing the next() sibling node
            if ($node === $text1) {
                $emphasis->detach();
            }
        }

        $this->assertCount(6, $nodes); // All of the nodes were visited, including the detached sibling...
        $this->assertCount(4, \iterator_to_array($document->iterator())); // Even though that emphasis and its child were actually removed
    }
}
