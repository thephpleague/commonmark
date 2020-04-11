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

interface SlugGeneratorInterface
{
    /**
     * Create a URL-friendly slug based on the given input string
     *
     * @param string $input
     *
     * @return string
     */
    public function createSlug(string $input): string;
}
