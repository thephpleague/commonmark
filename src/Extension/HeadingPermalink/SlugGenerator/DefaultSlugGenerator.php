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

namespace League\CommonMark\Extension\HeadingPermalink\SlugGenerator;

use League\CommonMark\Extension\CommonMark\Node\Block\HtmlBlock;
use League\CommonMark\Extension\CommonMark\Node\Inline\HtmlInline;
use League\CommonMark\Node\Node;
use League\CommonMark\Node\StringContainerHelper;

/**
 * Creates URL-friendly strings based on the inner contents of a node and its descendants
 */
final class DefaultSlugGenerator implements SlugGeneratorInterface
{
    public function generateSlug(Node $node): string
    {
        $childText = StringContainerHelper::getChildText($node, [HtmlBlock::class, HtmlInline::class]);

        return self::slugifyText($childText);
    }

    /**
     * @internal This method is only public to facilitate internal testing. DO NOT RELY ON ITS EXISTENCE OR BEHAVIOR!
     */
    public static function slugifyText(string $text): string
    {
        // Trim whitespace
        $slug = \trim($text);
        // Convert to lowercase
        $slug = \mb_strtolower($slug);
        // Try replacing whitespace with a dash
        $slug = \preg_replace('/\s+/u', '-', $slug) ?? $slug;
        // Try removing characters other than letters, numbers, and marks.
        $slug = \preg_replace('/[^\p{L}\p{Nd}\p{Nl}\p{M}-]+/u', '', $slug) ?? $slug;

        return $slug;
    }
}
