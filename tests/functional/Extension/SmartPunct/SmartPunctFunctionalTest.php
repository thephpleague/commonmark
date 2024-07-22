<?php

declare(strict_types=1);

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (http://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Tests\Functional\Extension\SmartPunct;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\SmartPunct\SmartPunctExtension;
use League\CommonMark\MarkdownConverter;
use League\CommonMark\Tests\Functional\AbstractSpecTestCase;
use League\CommonMark\Util\SpecReader;

/**
 * Tests the parser against the CommonMark spec
 */
final class SmartPunctFunctionalTest extends AbstractSpecTestCase
{
    protected function setUp(): void
    {
        $environment = new Environment();
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new SmartPunctExtension());

        $this->converter = new MarkdownConverter($environment);
    }

    public static function dataProvider(): \Generator
    {
        yield from SpecReader::readFile(__DIR__ . '/../../../../vendor/commonmark/commonmark.js/test/smart_punct.txt');

        yield 'Existing formatted quotes should be preserved (issue #1030)' => [
            'input'   => 'In the middle to late ’90s, it was chaos. "We couldn\'t get out of that rut."',
            'output'  => "<p>In the middle to late ’90s, it was chaos. “We couldn’t get out of that rut.”</p>\n",
        ];

        yield 'already-formatted quotes are kept as-is' => [
            'input'   => '"Plain quotes", “normal quotes”, and ”backwards quotes“',
            'output'  => "<p>“Plain quotes”, “normal quotes”, and ”backwards quotes“</p>\n",
        ];
    }
}
