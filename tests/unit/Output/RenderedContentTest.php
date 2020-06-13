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

namespace League\CommonMark\Tests\Unit\Output;

use League\CommonMark\Node\Block\Document;
use League\CommonMark\Output\RenderedContent;
use PHPUnit\Framework\TestCase;

final class RenderedContentTest extends TestCase
{
    public function testEverything(): void
    {
        $document = $this->createMock(Document::class);
        $html     = '<h1>Hello World!</h1>';
        $object   = new RenderedContent($document, $html);

        $this->assertInstanceOf(\Stringable::class, $object);

        $this->assertSame($document, $object->getDocument());
        $this->assertSame($html, $object->getContent());
        $this->assertSame($html, $object->__toString());
        $this->assertSame($html, (string) $object);
    }
}
