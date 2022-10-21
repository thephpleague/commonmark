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

namespace League\CommonMark\Extension\HeadingPermalink;

use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Node\Inline\AbstractInline;

/**
 * Represents an anchor link within a heading
 */
final class HeadingPermalink extends AbstractInline
{
    /** @psalm-readonly */
    private string $slug;

    /** @psalm-readonly */
    private bool $attachedHeading;

    /** @psalm-readonly */
    private string $idPrefix;

    public function __construct(string $slug, string $idPrefix, bool $attachedHeading)
    {
        parent::__construct();

        $this->slug            = $slug;
        $this->idPrefix        = $idPrefix;
        $this->attachedHeading = $attachedHeading;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getIdPrefix(): string
    {
        return $this->idPrefix;
    }

    public function isAttachedHeading(): bool
    {
        return $this->attachedHeading;
    }
}
