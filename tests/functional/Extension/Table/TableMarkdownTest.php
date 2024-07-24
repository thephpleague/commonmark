<?php

declare(strict_types=1);

/*
 * This is part of the league/commonmark package.
 *
 * (c) Martin Hasoň <martin.hason@gmail.com>
 * (c) Webuni s.r.o. <info@webuni.cz>
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Tests\Functional\Extension\Table;

use League\CommonMark\ConverterInterface;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\Table\Table;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\MarkdownConverter;
use League\CommonMark\Parser\MarkdownParser;
use League\CommonMark\Tests\Functional\AbstractLocalDataTestCase;

/**
 * @internal
 */
final class TableMarkdownTest extends AbstractLocalDataTestCase
{
    /**
     * @param array<string, mixed> $config
     */
    protected function createConverter(array $config = []): ConverterInterface
    {
        $environment = new Environment($config);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new TableExtension());

        return new MarkdownConverter($environment);
    }

    /**
     * {@inheritDoc}
     */
    public static function dataProvider(): iterable
    {
        yield from self::loadTests(__DIR__ . '/md');
    }

    public function testStartEndLinesProperlySet(): void
    {
        $markdown = <<<MD

## Tabelle

| Datum | Programm | Ort |
| --- | --- | --- |
| 22. Mai | Anreise | Eichberg |
| 23. Mai | Programm | Eichberg |
MD;

        $environment = new Environment([]);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new TableExtension());

        $parser = new MarkdownParser($environment);
        $doc    = $parser->parse($markdown);

        $table = $doc->lastChild();
        $this->assertInstanceOf(Table::class, $table);
        $this->assertSame(4, $table->getStartLine());
        $this->assertSame(7, $table->getEndLine());
    }
}
