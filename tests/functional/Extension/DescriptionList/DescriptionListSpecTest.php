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

namespace League\CommonMark\Tests\Functional\Extension\DescriptionList;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\DescriptionList\DescriptionListExtension;
use League\CommonMark\MarkdownConverter;
use League\CommonMark\Tests\Functional\AbstractSpecTestCase;
use League\CommonMark\Util\SpecReader;

final class DescriptionListSpecTest extends AbstractSpecTestCase
{
    protected function setUp(): void
    {
        $environment = new Environment();
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new DescriptionListExtension());

        $this->converter = new MarkdownConverter($environment);
    }

    public static function dataProvider(): \Generator
    {
        yield from SpecReader::readFile(__DIR__ . '/spec.txt');
    }
}
