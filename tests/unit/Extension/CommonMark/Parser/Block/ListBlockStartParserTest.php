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

namespace League\CommonMark\Tests\Unit\Extension\CommonMark\Parser\Block;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\CommonMark\Node\Block\ListBlock;
use League\CommonMark\Extension\CommonMark\Node\Block\ListItem;
use League\CommonMark\Extension\CommonMark\Parser\Block\ListBlockStartParser;
use League\CommonMark\Parser\Cursor;
use League\CommonMark\Parser\MarkdownParserStateInterface;
use League\Config\ConfigurationInterface;
use League\Config\Exception\InvalidConfigurationException;
use PHPUnit\Framework\TestCase;

final class ListBlockStartParserTest extends TestCase
{
    public function testOrderedListStartingAtOne(): void
    {
        $cursor = new Cursor('1. Foo');

        $parser = new ListBlockStartParser();
        $parser->setConfiguration($this->createConfiguration());
        $start = $parser->tryStart($cursor, $this->createMock(MarkdownParserStateInterface::class));

        $this->assertNotNull($start);

        $parsers = $start->getBlockParsers();
        $this->assertCount(2, $parsers);

        $block = $parsers[0]->getBlock();
        \assert($block instanceof ListBlock);
        $this->assertInstanceOf(ListBlock::class, $block);

        $item = $parsers[1]->getBlock();
        \assert($item instanceof ListItem);
        $this->assertInstanceOf(ListItem::class, $item);

        $this->assertSame(ListBlock::TYPE_ORDERED, $block->getListData()->type);
        $this->assertSame(1, $block->getListData()->start);

        $this->assertSame(ListBlock::TYPE_ORDERED, $item->getListData()->type);
    }

    public function testOrderedListStartingAtTwo(): void
    {
        $cursor = new Cursor('2. Foo');

        $parser = new ListBlockStartParser();
        $parser->setConfiguration($this->createConfiguration());

        $start = $parser->tryStart($cursor, $this->createMock(MarkdownParserStateInterface::class));

        $this->assertNotNull($start);

        $parsers = $start->getBlockParsers();
        $this->assertCount(2, $parsers);

        $block = $parsers[0]->getBlock();
        \assert($block instanceof ListBlock);
        $this->assertInstanceOf(ListBlock::class, $block);

        $item = $parsers[1]->getBlock();
        \assert($item instanceof ListItem);
        $this->assertInstanceOf(ListItem::class, $item);

        $this->assertSame(ListBlock::TYPE_ORDERED, $block->getListData()->type);
        $this->assertSame(2, $block->getListData()->start);

        $this->assertSame(ListBlock::TYPE_ORDERED, $item->getListData()->type);
    }

    public function testUnorderedListWithDashMarker(): void
    {
        $cursor = new Cursor('- Foo');

        $parser = new ListBlockStartParser();
        $parser->setConfiguration($this->createConfiguration());

        $start = $parser->tryStart($cursor, $this->createMock(MarkdownParserStateInterface::class));

        $this->assertNotNull($start);

        $parsers = $start->getBlockParsers();
        $this->assertCount(2, $parsers);

        $block = $parsers[0]->getBlock();
        \assert($block instanceof ListBlock);
        $this->assertInstanceOf(ListBlock::class, $block);

        $item = $parsers[1]->getBlock();
        \assert($item instanceof ListItem);
        $this->assertInstanceOf(ListItem::class, $item);

        $this->assertSame(ListBlock::TYPE_BULLET, $block->getListData()->type);
        $this->assertSame('-', $block->getListData()->bulletChar);

        $this->assertSame(ListBlock::TYPE_BULLET, $item->getListData()->type);
    }

    public function testUnorderedListWithAsteriskMarker(): void
    {
        $cursor = new Cursor('* Foo');

        $parser = new ListBlockStartParser();
        $parser->setConfiguration($this->createConfiguration());
        $start = $parser->tryStart($cursor, $this->createMock(MarkdownParserStateInterface::class));

        $this->assertNotNull($start);

        $parsers = $start->getBlockParsers();
        $this->assertCount(2, $parsers);

        $block = $parsers[0]->getBlock();
        \assert($block instanceof ListBlock);
        $this->assertInstanceOf(ListBlock::class, $block);

        $item = $parsers[1]->getBlock();
        \assert($item instanceof ListItem);
        $this->assertInstanceOf(ListItem::class, $item);

        $this->assertSame(ListBlock::TYPE_BULLET, $block->getListData()->type);
        $this->assertSame('*', $block->getListData()->bulletChar);

        $this->assertSame(ListBlock::TYPE_BULLET, $item->getListData()->type);
    }

    public function testUnorderedListWithPlusMarker(): void
    {
        $cursor = new Cursor('+ Foo');

        $parser = new ListBlockStartParser();
        $parser->setConfiguration($this->createConfiguration());
        $start = $parser->tryStart($cursor, $this->createMock(MarkdownParserStateInterface::class));

        $this->assertNotNull($start);

        $parsers = $start->getBlockParsers();
        $this->assertCount(2, $parsers);

        $block = $parsers[0]->getBlock();
        \assert($block instanceof ListBlock);
        $this->assertInstanceOf(ListBlock::class, $block);

        $item = $parsers[1]->getBlock();
        \assert($item instanceof ListItem);
        $this->assertInstanceOf(ListItem::class, $item);

        $this->assertSame(ListBlock::TYPE_BULLET, $block->getListData()->type);
        $this->assertSame('+', $block->getListData()->bulletChar);

        $this->assertSame(ListBlock::TYPE_BULLET, $item->getListData()->type);
    }

    public function testUnorderedListWithCustomMarker(): void
    {
        $cursor = new Cursor('^ Foo');

        $parser = new ListBlockStartParser();
        $parser->setConfiguration($this->createConfiguration(['commonmark' => ['unordered_list_markers' => ['^']]]));
        $start = $parser->tryStart($cursor, $this->createMock(MarkdownParserStateInterface::class));

        $this->assertNotNull($start);

        $parsers = $start->getBlockParsers();
        $this->assertCount(2, $parsers);

        $block = $parsers[0]->getBlock();
        \assert($block instanceof ListBlock);
        $this->assertInstanceOf(ListBlock::class, $block);

        $item = $parsers[1]->getBlock();
        \assert($item instanceof ListItem);
        $this->assertInstanceOf(ListItem::class, $item);

        $this->assertSame(ListBlock::TYPE_BULLET, $block->getListData()->type);
        $this->assertSame('^', $block->getListData()->bulletChar);

        $this->assertSame(ListBlock::TYPE_BULLET, $item->getListData()->type);
    }

    public function testUnorderedListWithDisabledMarker(): void
    {
        $cursor = new Cursor('+ Foo');

        $parser = new ListBlockStartParser();
        $parser->setConfiguration($this->createConfiguration(['commonmark' => ['unordered_list_markers' => ['-', '*']]]));
        $start = $parser->tryStart($cursor, $this->createMock(MarkdownParserStateInterface::class));

        $this->assertNull($start);
    }

    public function testInvalidListMarkerConfiguration(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessageMatches('/expects to be list/');

        $cursor = new Cursor('- Foo');

        $parser = new ListBlockStartParser();
        $parser->setConfiguration($this->createConfiguration(['commonmark' => ['unordered_list_markers' => '-']]));
        $parser->tryStart($cursor, $this->createMock(MarkdownParserStateInterface::class));
    }

    /**
     * @param array<string, mixed> $values
     */
    private function createConfiguration(array $values = []): ConfigurationInterface
    {
        $config = Environment::createDefaultConfiguration();
        (new CommonMarkCoreExtension())->configureSchema($config);
        $config->merge($values);

        return $config->reader();
    }
}
