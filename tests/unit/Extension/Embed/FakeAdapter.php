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

use League\CommonMark\Extension\Embed\Embed;
use League\CommonMark\Extension\Embed\EmbedAdapterInterface;

final class FakeAdapter implements EmbedAdapterInterface
{
    /** @var Embed[] */
    private array $updatedEmbeds = [];

    /**
     * {@inheritDoc}
     */
    public function updateEmbeds(array $embeds): void
    {
        foreach ($embeds as $embed) {
            \assert($embed instanceof Embed);
            $embed->setEmbedCode(\sprintf('<iframe class="embed" src="%s"></iframe>', $embed->getUrl()));

            $this->updatedEmbeds[] = $embed;
        }
    }

    /**
     * @return Embed[]
     */
    public function getUpdatedEmbeds(): array
    {
        return $this->updatedEmbeds;
    }
}
