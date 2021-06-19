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

namespace League\CommonMark\Tests\Unit\Node\Inline;

use League\CommonMark\Node\Block\Paragraph;
use League\CommonMark\Node\Inline\AdjacentTextMerger;
use League\CommonMark\Node\Inline\Newline;
use League\CommonMark\Node\Inline\Text;
use PHPUnit\Framework\TestCase;

final class AdjacentTextMergerTest extends TestCase
{
    public function testMergeTextNodesBetweenExclusive(): void
    {
        $paragraph = new Paragraph();

        $paragraph->appendChild($from = new Text('https://eventum.example.net/history.php?iss'));
        $paragraph->appendChild(new Text('_'));
        $paragraph->appendChild(new Text('id=107092'));
        $paragraph->appendChild(new Newline(Newline::SOFTBREAK));
        $paragraph->appendChild($to = new Text('https://gitlab.example.net/group/project/merge'));
        $paragraph->appendChild(new Text('_'));
        $paragraph->appendChild(new Text('requests/39#note'));
        $paragraph->appendChild(new Text('_'));
        $paragraph->appendChild(new Text('150630'));

        AdjacentTextMerger::mergeTextNodesBetweenExclusive($from, $to);

        $children = $paragraph->children();

        $this->assertCount(8, $children);

        $this->assertTrue($children[0] instanceof Text);
        $this->assertEquals('https://eventum.example.net/history.php?iss', $children[0]->getLiteral());

        $this->assertTrue($children[1] instanceof Text);
        $this->assertEquals('_id=107092', $children[1]->getLiteral());

        $this->assertTrue($children[2] instanceof Newline);
    }

    public function testMergeChildNodes(): void
    {
        $paragraph = new Paragraph();

        $paragraph->appendChild(new Text('https://eventum.example.net/history.php?iss'));
        $paragraph->appendChild(new Text('_'));
        $paragraph->appendChild(new Text('id=107092'));
        $paragraph->appendChild(new Newline(Newline::SOFTBREAK));
        $paragraph->appendChild(new Text('https://gitlab.example.net/group/project/merge'));
        $paragraph->appendChild(new Text('_'));
        $paragraph->appendChild(new Text('requests/39#note'));
        $paragraph->appendChild(new Text('_'));
        $paragraph->appendChild(new Text('150630'));

        AdjacentTextMerger::mergeChildNodes($paragraph);

        $children = $paragraph->children();

        $this->assertCount(3, $children);

        $this->assertTrue($children[0] instanceof Text);
        $this->assertEquals('https://eventum.example.net/history.php?iss_id=107092', $children[0]->getLiteral());

        $this->assertTrue($children[1] instanceof Newline);

        $this->assertTrue($children[2] instanceof Text);
        $this->assertEquals('https://gitlab.example.net/group/project/merge_requests/39#note_150630', $children[2]->getLiteral());
    }

    public function testMergeWithDirectlyAdjacentNodes(): void
    {
        $paragraph = new Paragraph();

        $paragraph->appendChild($text1 = new Text('commonmark.thephpleague.com'));

        AdjacentTextMerger::mergeWithDirectlyAdjacentNodes($text1);

        $this->assertCount(1, $paragraph->children());
        $this->assertSame($text1, $paragraph->firstChild());
        $this->assertSame('commonmark.thephpleague.com', $paragraph->firstChild()->getLiteral());

        $paragraph->appendChild(new Text('/2.0'));

        AdjacentTextMerger::mergeWithDirectlyAdjacentNodes($text1);

        $this->assertCount(1, $paragraph->children());
        $this->assertSame($text1, $paragraph->firstChild());
        $this->assertSame('commonmark.thephpleague.com/2.0', $paragraph->firstChild()->getLiteral());

        $paragraph->prependChild($new = new Text('://'));

        $this->assertSame($new, $text1->previous());
        $this->assertSame($text1, $new->next());
        $this->assertSame($paragraph, $text1->parent());
        $this->assertSame($paragraph, $new->parent());

        AdjacentTextMerger::mergeWithDirectlyAdjacentNodes($text1);

        $this->assertCount(1, $paragraph->children());
        $this->assertSame($new, $paragraph->firstChild());
        $this->assertSame('://commonmark.thephpleague.com/2.0', $paragraph->firstChild()->getLiteral());
        $this->assertNull($text1->previous());
        $this->assertNull($text1->next());
        $this->assertNull($text1->parent());
        $this->assertNull($new->previous());
        $this->assertNull($new->next());
        $this->assertSame($paragraph, $new->parent());

        $target = $paragraph->firstChild();

        $paragraph->prependChild($new = new Text('https'));
        $paragraph->appendChild(new Text('/'));

        AdjacentTextMerger::mergeWithDirectlyAdjacentNodes($target);

        $this->assertCount(1, $paragraph->children());
        $this->assertSame($new, $paragraph->firstChild());
        $this->assertSame('https://commonmark.thephpleague.com/2.0/', $paragraph->firstChild()->getLiteral());
    }
}
