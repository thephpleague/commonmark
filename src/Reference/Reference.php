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

/**
 * Link reference
 */
class Reference
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
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @return string
     */
    public function getDestination(): string
    {
        return $this->destination;
    }

    /**
     * @return string
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
        $string = \preg_replace('/\s+/', ' ', trim($string));

        return \mb_strtoupper($string, 'UTF-8');
    }
}
