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

namespace League\CommonMark;

/**
 * Converts CommonMark-compatible Markdown to HTML.
 */
class Converter
{
    /**
     * The document parser instance.
     *
     * @var \League\CommonMark\DocParser
     */
    protected $docParser;

    /**
     * The html renderer instance.
     *
     * @var \League\CommonMark\HtmlRendererInterface
     */
    protected $htmlRenderer;

    /**
     * @var \League\CommonMark\ElementTraverser|null
     */
    protected $elementTraverser;

    /**
     * Create a new commonmark converter instance.
     *
     * @param DocParser $docParser
     * @param HtmlRendererInterface $htmlRenderer
     * @param ElementTraverser|null $elementTraverser
     */
    public function __construct(DocParser $docParser, HtmlRendererInterface $htmlRenderer, ElementTraverser $elementTraverser = null)
    {
        $this->docParser = $docParser;
        $this->htmlRenderer = $htmlRenderer;
        $this->elementTraverser = $elementTraverser;
    }

    /**
     * Converts CommonMark to HTML.
     *
     * @param string $commonMark
     *
     * @return string
     *
     * @api
     */
    public function convertToHtml($commonMark)
    {
        $documentAST = $this->docParser->parse($commonMark);

        if (null !== $this->elementTraverser) {
            $documentAST = $this->elementTraverser->traverseBlock($documentAST);
        }

        return $this->htmlRenderer->renderBlock($documentAST);
    }
}
