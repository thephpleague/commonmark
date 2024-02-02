<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 * (c) Rezo Zero / Ambroise Maupate
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace League\CommonMark\Tests\Functional\Extension\Footnote;

use League\CommonMark\ConverterInterface;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\Footnote\FootnoteExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\MarkdownConverter;
use League\CommonMark\Tests\Functional\AbstractLocalDataTestCase;

/**
 * Test with other extensions
 */
final class FootnoteExtensionGFMTest extends AbstractLocalDataTestCase
{
    /**
     * @param array<string, mixed> $config
     */
    protected function createConverter(array $config = []): ConverterInterface
    {
        $gfmEnvironment = new Environment($config);
        $gfmEnvironment->addExtension(new CommonMarkCoreExtension());
        $gfmEnvironment->addExtension(new GithubFlavoredMarkdownExtension());
        $gfmEnvironment->addExtension(new FootnoteExtension());

        return new MarkdownConverter($gfmEnvironment);
    }

    /**
     * {@inheritDoc}
     */
    public static function dataProvider(): iterable
    {
        yield from self::loadTests(__DIR__ . '/md');
    }
}
