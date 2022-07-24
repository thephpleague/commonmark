<?php

declare(strict_types=1);

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (https://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Tests\Unit\Node\Block;

use League\CommonMark\Exception\InvalidArgumentException;
use League\CommonMark\Node\Block\AbstractBlock;
use League\CommonMark\Node\Inline\AbstractInline;
use PHPUnit\Framework\TestCase;

final class AbstractBlockTest extends TestCase
{
    public function testSetParent(): void
    {
        $block = $this->getMockForAbstractClass(AbstractBlock::class);

        $parent = $this->createMock(AbstractBlock::class);
        self::getMethod('setParent')->invoke($block, $parent);
        $this->assertSame($parent, $block->parent());

        self::getMethod('setParent')->invoke($block, null);
        $this->assertNull($block->parent());
    }

    public function testSetParentWithInvalidNode(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $block = $this->getMockForAbstractClass(AbstractBlock::class);

        $inline = $this->getMockForAbstractClass(AbstractInline::class);
        self::getMethod('setParent')->invoke($block, $inline);
    }

    public function testGetStartLine(): void
    {
        $block = $this->getMockForAbstractClass(AbstractBlock::class);

        self::getProperty('startLine')->setValue($block, 42);
        $this->assertEquals(42, $block->getStartLine());
    }

    public function testGetSetEndLine(): void
    {
        $block = $this->getMockForAbstractClass(AbstractBlock::class);

        $block->setEndLine(42);
        $this->assertEquals(42, $block->getEndLine());
    }

    private static function getMethod(string $name): \ReflectionMethod
    {
        $class  = new \ReflectionClass(AbstractBlock::class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);

        return $method;
    }

    private static function getProperty(string $name): \ReflectionProperty
    {
        $class    = new \ReflectionClass(AbstractBlock::class);
        $property = $class->getProperty($name);
        $property->setAccessible(true);

        return $property;
    }
}
