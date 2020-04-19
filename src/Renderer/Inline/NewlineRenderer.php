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

namespace League\CommonMark\Renderer\Inline;

use League\CommonMark\Node\Inline\AbstractInline;
use League\CommonMark\Node\Inline\Newline;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;

final class NewlineRenderer implements InlineRendererInterface
{
    /**
     * @param Newline               $inline
     * @param NodeRendererInterface $htmlRenderer
     *
     * @return HtmlElement|string
     */
    public function render(AbstractInline $inline, NodeRendererInterface $htmlRenderer)
    {
        if (!($inline instanceof Newline)) {
            throw new \InvalidArgumentException('Incompatible inline type: ' . \get_class($inline));
        }

        if ($inline->getType() === Newline::HARDBREAK) {
            return new HtmlElement('br', [], '', true) . "\n";
        }

        return $htmlRenderer->getOption('soft_break', "\n");
    }
}
