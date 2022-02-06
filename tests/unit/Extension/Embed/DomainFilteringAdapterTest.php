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

namespace League\CommonMark\Tests\Unit\Extension\Embed;

use League\CommonMark\Extension\Embed\DomainFilteringAdapter;
use League\CommonMark\Extension\Embed\Embed;
use League\CommonMark\Extension\Embed\EmbedAdapterInterface;
use PHPUnit\Framework\TestCase;

final class DomainFilteringAdapterTest extends TestCase
{
    public function testUpdateEmbeds(): void
    {
        $inner = new class implements EmbedAdapterInterface {
            /**
             * {@inheritDoc}
             */
            public function updateEmbeds(array $embeds): void
            {
                foreach ($embeds as $embed) {
                    $embed->setEmbedCode('some html');
                }
            }
        };

        $adapter = new DomainFilteringAdapter($inner, ['example.com', 'foo.bar.com']);

        $embeds = [
            new Embed('example.com'),
            new Embed('foo.example.com'),
            new Embed('http://foo.bar.com'),
            new Embed('https://foo.bar.com/baz'),
            new Embed('https://bar.com'),
            new Embed('www.bar.com'),
            new Embed('badexample.com'),
        ];

        $adapter->updateEmbeds($embeds);

        $this->assertSame('some html', $embeds[0]->getEmbedCode());
        $this->assertSame('some html', $embeds[1]->getEmbedCode());
        $this->assertSame('some html', $embeds[2]->getEmbedCode());
        $this->assertSame('some html', $embeds[3]->getEmbedCode());
        $this->assertNull($embeds[4]->getEmbedCode());
        $this->assertNull($embeds[5]->getEmbedCode());
        $this->assertNull($embeds[6]->getEmbedCode());
    }
}
