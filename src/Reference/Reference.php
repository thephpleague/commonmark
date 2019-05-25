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

namespace League\CommonMark\Reference;

final class Reference implements ReferenceInterface
{
    /**
     * @var string
     */
    protected $label;

    /**
     * @var string
     */
    protected $destination;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var array
     *
     * Source: https://github.com/symfony/polyfill-mbstring/blob/master/Mbstring.php
     */
    private static $caseFold = [
        ['µ', 'ſ', "\xCD\x85", 'ς', "\xCF\x90", "\xCF\x91", "\xCF\x95", "\xCF\x96", "\xCF\xB0", "\xCF\xB1", "\xCF\xB5", "\xE1\xBA\x9B", "\xE1\xBE\xBE", "\xC3\x9F", "\xE1\xBA\x9E"],
        ['μ', 's', 'ι',        'σ', 'β',        'θ',        'φ',        'π',        'κ',        'ρ',        'ε',        "\xE1\xB9\xA1", 'ι',            'ss',       'ss'],
    ];

    /**
     * Constructor
     *
     * @param string $label
     * @param string $destination
     * @param string $title
     */
    public function __construct(string $label, string $destination, string $title)
    {
        $this->label = self::normalizeReference($label);
        $this->destination = $destination;
        $this->title = $title;
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * {@inheritdoc}
     */
    public function getDestination(): string
    {
        return $this->destination;
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Normalize reference label
     *
     * This enables case-insensitive label matching
     *
     * @param string $string
     *
     * @return string
     */
    public static function normalizeReference(string $string): string
    {
        // Collapse internal whitespace to single space and remove
        // leading/trailing whitespace
        $string = \preg_replace('/\s+/', ' ', \trim($string));

        if (!\defined('MB_CASE_FOLD')) {
            // We're not on a version of PHP (7.3+) which has this feature
            $string = \str_replace(self::$caseFold[0], self::$caseFold[1], $string);

            return \mb_strtoupper($string, 'UTF-8');
        }

        return \mb_convert_case($string, \MB_CASE_FOLD, 'UTF-8');
    }
}
