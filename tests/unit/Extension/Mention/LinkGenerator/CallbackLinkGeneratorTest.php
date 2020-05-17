<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Tests\Unit\Extension\Mention\LinkGenerator;

use League\CommonMark\Extension\Mention\LinkGenerator\CallbackLinkGenerator;
use League\CommonMark\Inline\Element\Text;
use PHPUnit\Framework\TestCase;

final class CallbackLinkGeneratorTest extends TestCase
{
    public function testWithStringReturn(): void
    {
        $generator = new CallbackLinkGenerator(function (string $handle, string &$label, string $symbol): ?string {
            // Stuff the three params into the URL just to prove we received them all properly
            $url = \sprintf('https://www.example.com/%s/%s/%s', $handle, $label, $symbol);

            // Change the label (by reference)
            $label = 'New Label';

            return $url;
        });

        $link = $generator->generateLink('@', 'colinodell');

        $this->assertSame('https://www.example.com/colinodell/@colinodell/@', $link->getUrl());

        $label = $link->firstChild();
        assert($label instanceof Text);
        $this->assertSame('New Label', $label->getContent());
    }

    public function testWithNullReturn(): void
    {
        $generator = new CallbackLinkGenerator(function (string $handle, string &$label, string $symbol): ?string {
            return null;
        });

        $link = $generator->generateLink('@', 'colinodell');

        $this->assertNull($link);
    }

    public function testWithInvalidReturn(): void
    {
        $this->expectException(\RuntimeException::class);

        $generator = new CallbackLinkGenerator(function () {
            return new \stdClass(); // something that is not a string or null
        });

        $generator->generateLink('@', 'colinodell');
    }
}
