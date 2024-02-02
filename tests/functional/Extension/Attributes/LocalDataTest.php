<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 * (c) 2015 Martin Hasoň <martin.hason@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace League\CommonMark\Tests\Functional\Extension\Attributes;

use League\CommonMark\ConverterInterface;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\Attributes\AttributesExtension;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\MarkdownConverter;
use League\CommonMark\Tests\Functional\AbstractLocalDataTestCase;

/**
 * @internal
 */
final class LocalDataTest extends AbstractLocalDataTestCase
{
    /**
     * @param array<string, mixed> $config
     */
    protected function createConverter(array $config = []): ConverterInterface
    {
        $environment = new Environment($config);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new GithubFlavoredMarkdownExtension());
        $environment->addExtension(new AttributesExtension());

        return new MarkdownConverter($environment);
    }

    /**
     * {@inheritDoc}
     */
    public static function dataProvider(): iterable
    {
        yield from self::loadTests(__DIR__ . '/data');
    }
}
