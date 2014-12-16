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
        $environment = Environment::createCommonMarkEnvironment();
        $docParser = new DocParser($environment);
        $renderer = new HtmlRenderer($environment);

        $documentAST = $docParser->parse($commonMark);
        $html = $renderer->renderBlock($documentAST);

        return $html;
    }
}
