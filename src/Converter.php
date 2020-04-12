<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark;

/**
 * Converts CommonMark-compatible Markdown to HTML.
 */
class Converter implements ConverterInterface
{
    /**
     * The document parser instance.
     *
     * @var DocParserInterface
     */
    protected $docParser;

    /**
     * The html renderer instance.
     *
     * @var ElementRendererInterface
     */
    protected $htmlRenderer;

    /**
     * Create a new commonmark converter instance.
     *
     * @param DocParserInterface       $docParser
     * @param ElementRendererInterface $htmlRenderer
     *
     * @deprecated Instantiating a Converter class with a DocParserInterface and ElementRendererInterface is deprecated since league/commonmark 1.4. In 2.0, this constructor will require a configuration array and EnvironmentInterface.
     */
    public function __construct(DocParserInterface $docParser, ElementRendererInterface $htmlRenderer)
    {
        @trigger_error('Instantiating a "Converter" class with a DocParserInterface and ElementRendererInterface is deprecated since league/commonmark 1.4. In 2.0, this constructor will require a configuration array and EnvironmentInterface.', E_USER_DEPRECATED);

        $this->docParser = $docParser;
        $this->htmlRenderer = $htmlRenderer;
    }

    /**
     * Converts CommonMark to HTML.
     *
     * @param string $commonMark
     *
     * @throws \RuntimeException
     *
     * @return string
     *
     * @api
     */
    public function convertToHtml(string $commonMark): string
    {
        $documentAST = $this->docParser->parse($commonMark);

        return $this->htmlRenderer->renderBlock($documentAST);
    }

    /**
     * Converts CommonMark to HTML.
     *
     * @see Converter::convertToHtml
     *
     * @param string $commonMark
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    public function __invoke(string $commonMark): string
    {
        return $this->convertToHtml($commonMark);
    }
}
