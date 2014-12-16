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

interface InlineRendererInterface
{
    /**
     * @param AbstractBaseInline $inline
     * @param HtmlRenderer $htmlRenderer
     *
     * @return string
     */
    public function render(AbstractBaseInline $inline, HtmlRenderer $htmlRenderer);
}
