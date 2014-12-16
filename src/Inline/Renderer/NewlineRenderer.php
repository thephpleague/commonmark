<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (http://bitly.com/commonmarkjs)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Inline\Renderer;

use League\CommonMark\HtmlRenderer;
use League\CommonMark\Inline\Element\AbstractBaseInline;
use League\CommonMark\Inline\Element\Newline;

class NewlineRenderer implements InlineRendererInterface
{
    /**
     * @param Newline $inline
     * @param HtmlRenderer $htmlRenderer
     *
     * @return string
     */
    public function render(AbstractBaseInline $inline, HtmlRenderer $htmlRenderer)
    {
        if (!($inline instanceof Newline)) {
            throw new \InvalidArgumentException('Incompatible inline type: ' . get_class($inline));
        }

        if ($inline->getType() === Newline::HARDBREAK) {
            return $htmlRenderer->inTags('br', array(), '', true) . "\n";
        } else {
            return $htmlRenderer->getOption('softBreak');
        }
    }
}
