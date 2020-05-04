<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Extension\HeadingPermalink\Slug;

/**
 * Creates URL-friendly strings
 */
final class DefaultSlugGenerator implements SlugGeneratorInterface
{
    public function createSlug(string $input): string
    {
        // Trim whitespace
        $slug = \trim($input);
        // Convert to lowercase
        $slug = \mb_strtolower($slug);
        // Try replacing whitespace with a dash
        $slug = \preg_replace('/\s+/u', '-', $slug) ?? $slug;
        // Try removing characters other than letters, numbers, and marks.
        $slug = \preg_replace('/[^\p{L}\p{Nd}\p{Nl}\p{M}-]+/u', '', $slug) ?? $slug;

        return $slug;
    }
}
