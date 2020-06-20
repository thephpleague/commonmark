<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Tests\Unit\Extension\Mention\Generator;

use League\CommonMark\Extension\Mention\Generator\CallbackGenerator;
use League\CommonMark\Extension\Mention\Mention;
use League\CommonMark\Inline\Element\Emphasis;
use League\CommonMark\Inline\Element\Text;
use PHPUnit\Framework\TestCase;

final class CallbackGeneratorTest extends TestCase
{
    public function testWithStringReturn(): void
    {
        $generator = new CallbackGenerator(function (Mention $mention) {
            return $mention
                // Stuff the three params into the URL just to prove we received them all properly
                ->setUrl(\sprintf('https://www.example.com/%s/%s/%s', $mention->getIdentifier(), $mention->getLabel(), $mention->getSymbol()))
                // Change the label
                ->setLabel('New Label');
        });

        $mention = $generator->generateMention(new Mention('@', 'colinodell'));

        $this->assertSame('https://www.example.com/colinodell/@colinodell/@', $mention->getUrl());

        $label = $mention->firstChild();
        assert($label instanceof Text);
        $this->assertSame('New Label', $label->getContent());
    }

    public function testWithNullReturn(): void
    {
        $generator = new CallbackGenerator(function (Mention $mention) {
            return null;
        });

        $mention = $generator->generateMention(new Mention('@', 'colinodell'));

        $this->assertNull($mention);
    }

    public function testWithAbstractInlineReturn(): void
    {
        $emphasis = new Emphasis();
        $emphasis->appendChild(new Text('[members only]'));

        $generator = new CallbackGenerator(function (Mention $mention) use ($emphasis) {
            // Pretend callback does some access logic to determine visibility.
            return $emphasis;
        });

        $mention = $generator->generateMention(new Mention('@', 'colinodell'));

        $this->assertSame($emphasis, $mention);

        $label = $mention->firstChild();
        assert($label instanceof Text);
        $this->assertSame('[members only]', $label->getContent());
    }

    public function testWithNoUrlMentionReturn(): void
    {
        $this->expectException(\RuntimeException::class);

        $generator = new CallbackGenerator(function (Mention $mention) {
            // This ensures that if the URL is not set, but the mention is
            // returned (which is inherited from AbstractInline, then an
            // exception is properly thrown.
            return $mention;
        });

        $generator->generateMention(new Mention('@', 'colinodell'));
    }

    public function testWithNewMentionButNoUrlReturn(): void
    {
        $this->expectException(\RuntimeException::class);

        $generator = new CallbackGenerator(function (Mention $mention) {
            // Test what happens when returning a new mention without a URL
            return new Mention('@', 'foo');
        });

        $generator->generateMention(new Mention('@', 'colinodell'));
    }

    public function testWithInvalidReturn(): void
    {
        $this->expectException(\RuntimeException::class);

        $generator = new CallbackGenerator(function () {
            return new \stdClass(); // something that is not a string or null
        });

        $generator->generateMention(new Mention('@', 'colinodell'));
    }
}
