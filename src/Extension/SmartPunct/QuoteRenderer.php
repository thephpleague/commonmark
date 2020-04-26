<?php

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

use League\CommonMark\Node\Inline\AbstractInline;
use League\CommonMark\Renderer\Inline\InlineRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;

final class QuoteRenderer implements InlineRendererInterface
{
    /**
     * @param Quote                 $inline
     * @param NodeRendererInterface $htmlRenderer
     *
     * @return HtmlElement|string|null
     */
    public function render(AbstractInline $inline, NodeRendererInterface $htmlRenderer)
    {
        if (!$inline instanceof Quote) {
            throw new \InvalidArgumentException(sprintf('Expected an instance of "%s", got "%s" instead', Quote::class, get_class($inline)));
        }

        // Handles unpaired quotes which remain after processing delimiters
        if ($inline->getLiteral() === Quote::SINGLE_QUOTE) {
            // Render as an apostrophe
            return Quote::SINGLE_QUOTE_CLOSER;
        } elseif ($inline->getLiteral() === Quote::DOUBLE_QUOTE) {
            // Render as an opening quote
            return Quote::DOUBLE_QUOTE_OPENER;
        }

        return $inline->getLiteral();
    }
}
