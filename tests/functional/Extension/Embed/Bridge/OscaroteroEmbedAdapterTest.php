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

namespace League\CommonMark\Tests\Functional\Extension\Embed\Bridge;

use Embed\Embed as EmbedLib;
use Embed\Http\Crawler;
use League\CommonMark\ConverterInterface;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\Embed\Bridge\OscaroteroEmbedAdapter;
use League\CommonMark\Extension\Embed\EmbedExtension;
use League\CommonMark\MarkdownConverter;
use League\CommonMark\Tests\Functional\AbstractLocalDataTestCase;

final class OscaroteroEmbedAdapterTest extends AbstractLocalDataTestCase
{
    /**
     * {@inheritDoc}
     */
    protected function createConverter(array $config = []): ConverterInterface
    {
        $config['embed']['adapter'] = new OscaroteroEmbedAdapter(new EmbedLib(new Crawler(new LocalFileClient(__DIR__ . '/requests'))));

        $environment = new Environment($config);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new EmbedExtension());

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
