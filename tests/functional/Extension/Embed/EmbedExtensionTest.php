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

namespace League\CommonMark\Tests\Functional\Extension\Embed;

use League\CommonMark\ConverterInterface;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\Autolink\AutolinkExtension;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\Embed\EmbedExtension;
use League\CommonMark\MarkdownConverter;
use League\CommonMark\Tests\Functional\AbstractLocalDataTestCase;
use League\CommonMark\Tests\Unit\Extension\Embed\FakeAdapter;

final class EmbedExtensionTest extends AbstractLocalDataTestCase
{
    /**
     * {@inheritDoc}
     */
    protected function createConverter(array $config = []): ConverterInterface
    {
        $config['embed']['adapter'] = new FakeAdapter();

        $enableAutolinkExtension = $config['enable_autolinks'] ?? false;
        unset($config['enable_autolinks']);

        $environment = new Environment($config);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new EmbedExtension());

        if ($enableAutolinkExtension) {
            $environment->addExtension(new AutolinkExtension());
        }

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
