<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark;

/**
 * Interface for a service which converts CommonMark to HTML.
 */
interface ConverterInterface
{
    /**
     * Converts CommonMark to HTML.
     *
     * @param string $commonMark
     *
     * @return string HTML
     *
     * @api
     */
    public function convertToHtml(string $commonMark): string;
}
