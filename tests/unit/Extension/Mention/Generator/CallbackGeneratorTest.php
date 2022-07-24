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

namespace League\CommonMark\Tests\Unit\Extension\Mention\Generator;

use League\CommonMark\Exception\LogicException;
use League\CommonMark\Extension\CommonMark\Node\Inline\Emphasis;
use League\CommonMark\Extension\Mention\Generator\CallbackGenerator;
use League\CommonMark\Extension\Mention\Mention;
use League\CommonMark\Node\Inline\Text;
use PHPUnit\Framework\TestCase;

final class CallbackGeneratorTest extends TestCase
{
    public function testWithStringReturn(): void
    {
        $generator = new CallbackGenerator(static function (Mention $mention) {
            // Stuff the three params into the URL just to prove we received them all properly
            $mention->setUrl(\sprintf('https://www.example.com/%s/%s/%s', $mention->getIdentifier(), $mention->getLabel(), $mention->getPrefix()));
            // Change the label
            $mention->setLabel('New Label');

            return $mention;
        });

        $mention = $generator->generateMention(new Mention('test', '@', 'colinodell'));

        $this->assertSame('https://www.example.com/colinodell/@colinodell/@', $mention->getUrl());

        $label = $mention->firstChild();
        \assert($label instanceof Text);
        $this->assertSame('New Label', $label->getLiteral());
    }

    public function testWithNullReturn(): void
    {
        $generator = new CallbackGenerator(static function (Mention $mention) {
            return null;
        });

        $mention = $generator->generateMention(new Mention('test', '@', 'colinodell'));

        $this->assertNull($mention);
    }

    public function testWithAbstractInlineReturn(): void
    {
        $emphasis = new Emphasis('_');
        $emphasis->appendChild(new Text('[members only]'));

        $generator = new CallbackGenerator(static function (Mention $mention) use ($emphasis) {
            // Pretend callback does some access logic to determine visibility.
            return $emphasis;
        });

        $mention = $generator->generateMention(new Mention('test', '@', 'colinodell'));

        $this->assertSame($emphasis, $mention);

        $label = $mention->firstChild();
        \assert($label instanceof Text);
        $this->assertSame('[members only]', $label->getLiteral());
    }

    public function testWithNoUrlMentionReturn(): void
    {
        $this->expectException(LogicException::class);

        $generator = new CallbackGenerator(static function (Mention $mention) {
            // This ensures that if the URL is not set, but the mention is
            // returned (which is inherited from AbstractInline, then an
            // exception is properly thrown.
            return $mention;
        });

        $generator->generateMention(new Mention('test', '@', 'colinodell'));
    }

    public function testWithNewMentionButNoUrlReturn(): void
    {
        $this->expectException(LogicException::class);

        $generator = new CallbackGenerator(static function (Mention $mention) {
            // Test what happens when returning a new mention without a URL
            return new Mention('test', '@', 'foo');
        });

        $generator->generateMention(new Mention('test', '@', 'colinodell'));
    }

    public function testWithInvalidReturn(): void
    {
        $this->expectException(LogicException::class);

        $generator = new CallbackGenerator(static function () {
            return new \stdClass(); // something that is not a string or null
        });

        $generator->generateMention(new Mention('test', '@', 'colinodell'));
    }
}
