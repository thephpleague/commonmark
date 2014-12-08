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

namespace League\CommonMark;

/**
 * Converts CommonMark-compatible Markdown to HTML
 */
class CommonMarkConverter
{
    /**
     * Converts CommonMark to HTML
     * @param string $commonMark
     *
     * @return string
     *
     * @api
     */
    public function convertToHtml($commonMark)
    {
        $docParser = new DocParser();
        $renderer = new HtmlRenderer();

        $documentAST = $docParser->parse($commonMark);
        $html = $renderer->render($documentAST);

        return $html;
    }
}
