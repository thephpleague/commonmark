<?php

declare(strict_types=1);

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (http://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Extension\SmartPunct;

use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;

final class QuoteRenderer implements NodeRendererInterface
{
    /**
     * @param Quote $node
     *
     * {@inheritdoc}
     *
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public function render(Node $node, ChildNodeRendererInterface $childRenderer)
    {
        if (! $node instanceof Quote) {
            throw new \InvalidArgumentException(\sprintf('Expected an instance of "%s", got "%s" instead', Quote::class, \get_class($node)));
        }

        // Handles unpaired quotes which remain after processing delimiters
        if ($node->getLiteral() === Quote::SINGLE_QUOTE) {
            // Render as an apostrophe
            return Quote::SINGLE_QUOTE_CLOSER;
        }

        if ($node->getLiteral() === Quote::DOUBLE_QUOTE) {
            // Render as an opening quote
            return Quote::DOUBLE_QUOTE_OPENER;
        }

        return $node->getLiteral();
    }
}
