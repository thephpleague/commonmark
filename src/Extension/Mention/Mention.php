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

namespace League\CommonMark\Extension\Mention;

use League\CommonMark\Inline\Element\Link;
use League\CommonMark\Inline\Element\Text;

class Mention extends Link
{
    /** @var string */
    private $symbol;

    /** @var string */
    private $match;

    /** @var Text */
    private $label;

    /**
     * @param string $symbol
     * @param string $match
     * @param string $label
     */
    public function __construct(string $symbol, string $match, string $label = null)
    {
        $this->symbol = $symbol;
        $this->match = $match;

        if (empty($label)) {
            $label = "$symbol$match";
        }

        $this->label = new Text($label);
        $this->appendChild($this->label);
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label->getContent();
    }

    /**
     * @return string
     */
    public function getMatch(): string
    {
        return $this->match;
    }

    /**
     * @return string
     */
    public function getSymbol(): string
    {
        return $this->symbol;
    }

    /**
     * @return bool
     */
    public function hasUrl(): bool
    {
        return isset($this->url);
    }

    /**
     * @param string $label
     *
     * @return $this
     */
    public function setLabel(string $label): self
    {
        $this->label->setContent($label);

        return $this;
    }
}
