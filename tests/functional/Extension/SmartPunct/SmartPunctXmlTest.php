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

namespace League\CommonMark\Tests\Functional\Extension\SmartPunct;

use League\CommonMark\ConverterInterface;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\SmartPunct\SmartPunctExtension;
use League\CommonMark\Tests\Functional\AbstractLocalDataTestCase;
use League\CommonMark\Xml\MarkdownToXmlConverter;

final class SmartPunctXmlTest extends AbstractLocalDataTestCase
{
    /**
     * @param array<string, mixed> $config
     */
    protected function createConverter(array $config = []): ConverterInterface
    {
        $environment = new Environment($config);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new SmartPunctExtension());

        return new MarkdownToXmlConverter($environment);
    }

    /**
     * {@inheritDoc}
     */
    public static function dataProvider(): iterable
    {
        yield from self::loadTests(__DIR__ . '/xml', '*', '.md', '.xml');
    }
}
