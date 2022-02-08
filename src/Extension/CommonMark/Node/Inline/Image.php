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

namespace League\CommonMark\Extension\CommonMark\Node\Inline;

use League\CommonMark\Node\Inline\AbstractStringContainer;
use League\CommonMark\Node\Inline\AdjacentTextMerger;
use League\CommonMark\Node\Inline\Text;
use League\CommonMark\Node\Node;

class Image extends AbstractWebResource
{
    protected ?string $title = null;

    public function __construct(string $url, ?string $label = null, ?string $title = null)
    {
        parent::__construct($url);

        if ($label !== null && $label !== '') {
            $this->appendChild(new Text($label));
        }

        $this->title = $title;
    }

    public function getTitle(): ?string
    {
        if ($this->title === '') {
            return null;
        }

        return $this->title;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    /**
     * Get label (typically used as the alt attribute) for the image.
     *
     * Per CM spec, the label of an image can contain other inline nodes. We
     * represent this by using the children of this node as the label. However,
     * we still need it to be "plain text" when rendered out into a tag. This
     * method attempts to build the label by extracting whatever text we know
     * how to extract from our child nodes.
     */
    public function getLabel(): string
    {
        $label = '';
        foreach ($this->children() as $child) {
            if ($child instanceof AbstractStringContainer) {
                $label .= $child->getLiteral();
            } elseif ($child instanceof Link) {
                $label .= $child->getTitle();
            } elseif ($child instanceof Image) {
                $label .= $child->getLabel();
            } elseif (
                $child instanceof Node
                && $child->hasChildren()
                && $child->firstChild() instanceof AbstractStringContainer
            ) {
                // Clone before merging so we don't manipulate the "real"
                // children.
                $clone = clone $child;
                AdjacentTextMerger::mergeChildNodes($clone);
                $label .= $clone->firstChild()->getLiteral();
            }
        }

        return $label;
    }
}
