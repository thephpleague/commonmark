<?php

namespace ColinODell\CommonMark;

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
