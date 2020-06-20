<?php

declare(strict_types=1);

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

use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Node\Inline\Text;

class Mention extends Link
{
    /** @var string */
    private $symbol;

    /** @var string */
    private $identifier;

    public function __construct(string $symbol, string $identifier, ?string $label = null)
    {
        $this->symbol     = $symbol;
        $this->identifier = $identifier;

        parent::__construct('', $label ?? \sprintf('%s%s', $symbol, $identifier));
    }

    public function getLabel(): ?string
    {
        if (($labelNode = $this->findLabelNode()) === null) {
            return null;
        }

        return $labelNode->getLiteral();
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getSymbol(): string
    {
        return $this->symbol;
    }

    public function hasUrl(): bool
    {
        return $this->url !== '';
    }

    /**
     * @return $this
     */
    public function setLabel(string $label): self
    {
        if (($labelNode = $this->findLabelNode()) === null) {
            $labelNode = new Text();
            $this->prependChild($labelNode);
        }

        $labelNode->setLiteral($label);

        return $this;
    }

    private function findLabelNode(): ?Text
    {
        foreach ($this->children() as $child) {
            if ($child instanceof Text) {
                return $child;
            }
        }

        return null;
    }
}
