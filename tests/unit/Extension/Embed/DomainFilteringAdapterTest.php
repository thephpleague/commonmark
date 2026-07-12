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
        $embeds = [
            new Embed('google.com'),
            $embed1 = new Embed('example.com'),
            $embed2 = new Embed('foo.example.com'),
            new Embed('www.bar.com'),
            new Embed('badexample.com'),
            $embed3 = new Embed('HTTP://foo.bar.com'),
            $embed4 = new Embed('hTtPs://foo.bar.com/baz'),
            new Embed('https://bar.com'),
            new Embed('https://example.com.evil'),
            new Embed('https://example.com.evil/path'),
            new Embed('https://foo.bar.com.evil'),
            new Embed('example.com.evil'),
            new Embed('example.com.evil/path'),
            new Embed('foo.bar.com.evil'),
            new Embed('https://example.com@evil.com'),
            new Embed('https://user:pass@evil.com'),
            new Embed('https://example.com:pass@evil.com/path'),
            new Embed('javascript:alert(1)'),
            new Embed('ftp://example.com'),
            new Embed('file:///etc/passwd'),
            new Embed('data:text/html,<script>alert(1)</script>'),
            new Embed('//example.com/path'),
        ];

        $inner = $this->createMock(EmbedAdapterInterface::class);
        $inner->expects($this->once())->method('updateEmbeds')->with([
            // It is critical that the filtered values have their keys re-indexed
            // See https://github.com/thephpleague/commonmark/issues/884
            0 => $embed1,
            1 => $embed2,
            2 => $embed3,
            3 => $embed4,
        ]);

        $adapter = new DomainFilteringAdapter($inner, ['example.com', 'foo.bar.com']);
        $adapter->updateEmbeds($embeds);
    }
}
