<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Tests\Unit\Input;

use League\CommonMark\Exception\UnexpectedEncodingException;
use League\CommonMark\Input\MarkdownInput;
use PHPUnit\Framework\TestCase;

final class MarkdownInputTest extends TestCase
{
    public function testConstructorAndGetter()
    {
        $markdown = new MarkdownInput('# Hello World!');

        $this->assertSame('# Hello World!', $markdown->getContent());
    }

    public function testInvalidContent()
    {
        $this->expectException(UnexpectedEncodingException::class);

        $markdown = new MarkdownInput(chr(250));
    }

    public function testGetLines()
    {
        $markdown = new MarkdownInput("# Hello World!\n\nThis is just a test.\n");

        $lines = $markdown->getLines();

        $this->assertSame(iterator_to_array($lines), [
            0 => '# Hello World!',
            1 => '',
            2 => 'This is just a test.',
        ]);
    }

    public function testGetLineCount()
    {
        $markdown = new MarkdownInput("# Hello World!\n\nThis is just a test.\n");

        $this->assertSame(3, $markdown->getLineCount());
    }
}
