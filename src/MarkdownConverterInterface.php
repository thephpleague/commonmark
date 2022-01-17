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

namespace League\CommonMark;

use League\CommonMark\Output\RenderedContentInterface;

\trigger_deprecation('league/commonmark', '2.2.0', 'The "%s" class is deprecated, use "%s" instead.', MarkdownConverterInterface::class, MarkdownConverter::class);

/**
 * Interface for a service which converts Markdown to HTML.
 *
 * @deprecated since 2.2; use {@link ConverterInterface} instead
 */
interface MarkdownConverterInterface
{
    /**
     * Converts Markdown to HTML.
     *
     * @deprecated since 2.2; use {@link ConverterInterface::convert()} instead
     *
     * @throws \RuntimeException
     */
    public function convertToHtml(string $markdown): RenderedContentInterface;
}
