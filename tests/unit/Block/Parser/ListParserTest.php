<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Tests\Unit\Block\Parser;

use League\CommonMark\Block\Element\Document;
use League\CommonMark\Block\Element\ListBlock;
use League\CommonMark\Block\Element\ListItem;
use League\CommonMark\Block\Parser\ListParser;
use League\CommonMark\Context;
use League\CommonMark\Cursor;
use League\CommonMark\Environment;
use League\CommonMark\Util\Configuration;
use PHPUnit\Framework\TestCase;

final class ListParserTest extends TestCase
{
    public function testOrderedListStartingAtOne()
    {
        $input = '1. Foo';

        $context = new Context(new Document(), new Environment());
        $context->setNextLine($input);
        $cursor = new Cursor($input);

        $parser = new ListParser();
        $this->assertTrue($parser->parse($context, $cursor));

        $container = $context->getContainer();

        $this->assertTrue($container instanceof ListItem);
        /** @var ListItem $container */
        $this->assertSame(ListBlock::TYPE_ORDERED, $container->getListData()->type);
        $this->assertSame(1, $container->getListData()->start);
    }

    public function testOrderedListStartingAtTwo()
    {
        $input = '2. Foo';

        $context = new Context(new Document(), new Environment());
        $context->setNextLine($input);
        $cursor = new Cursor($input);

        $parser = new ListParser();
        $this->assertTrue($parser->parse($context, $cursor));

        $container = $context->getContainer();

        $this->assertTrue($container instanceof ListItem);
        /** @var ListItem $container */
        $this->assertSame(ListBlock::TYPE_ORDERED, $container->getListData()->type);
        $this->assertSame(2, $container->getListData()->start);
    }

    public function testUnorderedListWithDashMarker()
    {
        $input = '- Foo';

        $context = new Context(new Document(), new Environment());
        $context->setNextLine($input);
        $cursor = new Cursor($input);

        $parser = new ListParser();
        $this->assertTrue($parser->parse($context, $cursor));

        $container = $context->getContainer();

        $this->assertTrue($container instanceof ListItem);
        /** @var ListItem $container */
        $this->assertSame(ListBlock::TYPE_BULLET, $container->getListData()->type);
        $this->assertSame('-', $container->getListData()->bulletChar);
    }

    public function testUnorderedListWithAsteriskMarker()
    {
        $input = '* Foo';

        $context = new Context(new Document(), new Environment());
        $context->setNextLine($input);
        $cursor = new Cursor($input);

        $parser = new ListParser();
        $this->assertTrue($parser->parse($context, $cursor));

        $container = $context->getContainer();

        $this->assertTrue($container instanceof ListItem);
        /** @var ListItem $container */
        $this->assertSame(ListBlock::TYPE_BULLET, $container->getListData()->type);
        $this->assertSame('*', $container->getListData()->bulletChar);
    }

    public function testUnorderedListWithPlusMarker()
    {
        $input = '+ Foo';

        $context = new Context(new Document(), new Environment());
        $context->setNextLine($input);
        $cursor = new Cursor($input);

        $parser = new ListParser();
        $this->assertTrue($parser->parse($context, $cursor));

        $container = $context->getContainer();

        $this->assertTrue($container instanceof ListItem);
        /** @var ListItem $container */
        $this->assertSame(ListBlock::TYPE_BULLET, $container->getListData()->type);
        $this->assertSame('+', $container->getListData()->bulletChar);
    }

    /**
     * @dataProvider configPaths
     */
    public function testUnorderedListWithCustomMarker(string $configPath)
    {
        $input = '^ Foo';

        $context = new Context(new Document(), new Environment());
        $context->setNextLine($input);
        $cursor = new Cursor($input);

        $config = new Configuration();
        $config->set($configPath, ['^']);

        $parser = new ListParser();
        $parser->setConfiguration($config);
        $this->assertTrue($parser->parse($context, $cursor));

        $container = $context->getContainer();

        $this->assertTrue($container instanceof ListItem);
        /** @var ListItem $container */
        $this->assertSame(ListBlock::TYPE_BULLET, $container->getListData()->type);
        $this->assertSame('^', $container->getListData()->bulletChar);
    }

    /**
     * @dataProvider configPaths
     */
    public function testUnorderedListWithDisabledMarker(string $configPath)
    {
        $input = '+ Foo';

        $context = new Context(new Document(), new Environment());
        $context->setNextLine($input);
        $cursor = new Cursor($input);

        $config = new Configuration();
        $config->set($configPath, ['-', '*']);

        $parser = new ListParser();
        $parser->setConfiguration($config);
        $this->assertFalse($parser->parse($context, $cursor));
    }

    /**
     * @dataProvider configPaths
     */
    public function testInvalidListMarkerConfiguration(string $configPath)
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Invalid configuration option "unordered_list_markers": value must be an array of strings');
        $input = '+ Foo';

        $context = new Context(new Document(), new Environment());
        $context->setNextLine($input);
        $cursor = new Cursor($input);

        $config = new Configuration();
        $config->set($configPath, '-');

        $parser = new ListParser();
        $parser->setConfiguration($config);

        $parser->parse($context, $cursor);
    }

    /**
     * @return iterable<string[]>
     */
    public function configPaths(): iterable
    {
        yield ['unordered_list_markers'];
        yield ['commonmark/unordered_list_markers'];
    }
}
