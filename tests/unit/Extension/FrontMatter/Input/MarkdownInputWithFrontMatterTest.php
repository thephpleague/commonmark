<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace League\CommonMark\Tests\Unit\Extension\FrontMatter\Input;

use League\CommonMark\Extension\FrontMatter\Input\MarkdownInputWithFrontMatter;
use PHPUnit\Framework\TestCase;

final class MarkdownInputWithFrontMatterTest extends TestCase
{
    public function testConstructorAndGetters(): void
    {
        $input = new MarkdownInputWithFrontMatter("this\nis\na\ntest\n", 3, ['foo' => 'bar']);

        $this->assertSame("this\nis\na\ntest\n", $input->getContent());

        $lines = $input->getLines();
        \assert($lines instanceof \Traversable);
        $this->assertSame([4 => 'this', 5 => 'is', 6 => 'a', 7 => 'test'], \iterator_to_array($lines));

        $this->assertSame(['foo' => 'bar'], $input->getFrontMatter());
    }
}
