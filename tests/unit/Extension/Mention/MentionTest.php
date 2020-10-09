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

namespace League\CommonMark\Tests\Unit\Extension\Mention;

use League\CommonMark\Extension\Mention\Mention;
use League\CommonMark\Node\Inline\Text;
use PHPUnit\Framework\TestCase;

final class MentionTest extends TestCase
{
    public function testConstructorAndGetters(): void
    {
        $mention = new Mention('test', '@', 'colinodell');

        $this->assertSame('test', $mention->getName());
        $this->assertSame('@', $mention->getPrefix());
        $this->assertSame('colinodell', $mention->getIdentifier());
        $this->assertSame('@colinodell', $mention->getLabel());
        $this->assertSame('', $mention->getUrl());

        $this->assertCount(1, $mention->children());
        $this->assertInstanceOf(Text::class, $child = $mention->firstChild());
        \assert($child instanceof Text);
        $this->assertSame('@colinodell', $child->getLiteral());
    }

    public function testConstructorAndGettersWithCustomLabel(): void
    {
        $mention = new Mention('test', '#', '123', 'Issue #123');

        $this->assertSame('#', $mention->getPrefix());
        $this->assertSame('123', $mention->getIdentifier());
        $this->assertSame('Issue #123', $mention->getLabel());
        $this->assertSame('', $mention->getUrl());

        $this->assertCount(1, $mention->children());
        $this->assertInstanceOf(Text::class, $child = $mention->firstChild());
        \assert($child instanceof Text);
        $this->assertSame('Issue #123', $child->getLiteral());
    }

    public function testSetUrl(): void
    {
        $mention = new Mention('test', '@', 'colinodell');
        $mention->setUrl('https://www.twitter.com/colinodell');

        $this->assertSame('https://www.twitter.com/colinodell', $mention->getUrl());
    }

    public function testSetLabel(): void
    {
        $mention = new Mention('test', '@', 'colinodell');

        $this->assertSame('@colinodell', $mention->getLabel());

        $this->assertCount(1, $mention->children());
        $this->assertInstanceOf(Text::class, $child = $mention->firstChild());
        \assert($child instanceof Text);
        $this->assertSame('@colinodell', $child->getLiteral());

        $mention->setLabel('Colin O\'Dell');

        $this->assertSame('Colin O\'Dell', $mention->getLabel());

        $this->assertCount(1, $mention->children());
        $this->assertInstanceOf(Text::class, $child2 = $mention->firstChild());
        \assert($child2 instanceof Text);
        $this->assertSame('Colin O\'Dell', $child2->getLiteral());

        $this->assertSame($child, $child2);
    }

    public function testLabelFunctionsWhenLabelDetached(): void
    {
        $mention = new Mention('test', '@', 'colinodell');

        $this->assertSame('@colinodell', $mention->getLabel());

        $mention->detachChildren();
        $this->assertCount(0, $mention->children());
        $this->assertNull($mention->getLabel());

        $mention->setLabel('Colin O\'Dell');

        $this->assertSame('Colin O\'Dell', $mention->getLabel());

        $this->assertCount(1, $mention->children());
        $this->assertInstanceOf(Text::class, $child = $mention->firstChild());
        \assert($child instanceof Text);
        $this->assertSame('Colin O\'Dell', $child->getLiteral());
    }
}
