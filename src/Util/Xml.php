<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (https://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Util;

/**
 * Utility class for handling/generating XML and HTML
 */
final class Xml
{
    /**
     * @param string $string
     * @param bool   $preserveEntities
     *
     * @return string
     */
    public static function escape($string, $preserveEntities = null)
    {
        if ($preserveEntities === true) {
            @trigger_error('Preserving entities in Xml::escape() has been deprecated and will be removed in the next release', E_USER_DEPRECATED);
            $string = preg_replace('/[&](?![#](x[a-f0-9]{1,8}|[0-9]{1,8});|[a-z][a-z0-9]{1,31};)/i', '&amp;', $string);
        } elseif ($preserveEntities === false) {
            @trigger_error('The $preserveEntities argument of Xml::escape() has been deprecated and will be removed in the next release', E_USER_DEPRECATED);
            $string = str_replace('&', '&amp;', $string);
        } else {
            $string = str_replace('&', '&amp;', $string);
        }

        return str_replace(['<', '>', '"'], ['&lt;', '&gt;', '&quot;'], $string);
    }
}
