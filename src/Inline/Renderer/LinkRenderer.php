<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on stmd.js
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Inline\Renderer;

use League\CommonMark\HtmlRenderer;
use League\CommonMark\Inline\Element\AbstractBaseInline;
use League\CommonMark\Inline\Element\Link;

class LinkRenderer implements InlineRendererInterface
{
    /**
     * @param Link $inline
     * @param HtmlRenderer $htmlRenderer
     *
     * @return string
     */
    public function render(AbstractBaseInline $inline, HtmlRenderer $htmlRenderer)
    {
        $attrs['href'] = $htmlRenderer->escape($inline->getUrl(), true);
        if (isset($inline->attributes['title'])) {
            $attrs['title'] = $htmlRenderer->escape($inline->attributes['title'], true);
        }

        return $htmlRenderer->inTags('a', $attrs, $htmlRenderer->renderInlines($inline->getLabel()->getInlines()));
    }
}
