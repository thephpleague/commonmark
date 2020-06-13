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

namespace League\CommonMark\Tests\Unit\Extension\FrontMatter\Output;

use League\CommonMark\Extension\FrontMatter\Output\RenderedContentWithFrontMatter;
use League\CommonMark\Node\Block\Document;
use PHPUnit\Framework\TestCase;

final class RenderedContentWithFrontMatterTest extends TestCase
{
    public function testConstructorAndGetters(): void
    {
        $document = new Document();

        $output = new RenderedContentWithFrontMatter($document, '<h1>Hello, World!</h1>', ['foo' => 'bar']);

        $this->assertSame($document, $output->getDocument());
        $this->assertSame('<h1>Hello, World!</h1>', $output->getContent());
        $this->assertSame(['foo' => 'bar'], $output->getFrontMatter());
    }
}
