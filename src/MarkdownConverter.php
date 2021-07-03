<?php

declare(strict_types=1);

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark;

use League\CommonMark\Environment\EnvironmentInterface;
use League\CommonMark\Output\RenderedContentInterface;
use League\CommonMark\Parser\MarkdownParser;
use League\CommonMark\Parser\MarkdownParserInterface;
use League\CommonMark\Renderer\HtmlRenderer;
use League\CommonMark\Renderer\MarkdownRendererInterface;

class MarkdownConverter implements MarkdownConverterInterface
{
    /** @psalm-readonly */
    protected EnvironmentInterface $environment;

    /** @psalm-readonly */
    protected MarkdownParserInterface $markdownParser;

    /** @psalm-readonly */
    protected MarkdownRendererInterface $htmlRenderer;

    public function __construct(EnvironmentInterface $environment)
    {
        $this->environment = $environment;

        $this->markdownParser = new MarkdownParser($environment);
        $this->htmlRenderer   = new HtmlRenderer($environment);
    }

    public function getEnvironment(): EnvironmentInterface
    {
        return $this->environment;
    }

    /**
     * Converts Markdown to HTML.
     *
     * @param string $markdown The Markdown to convert
     *
     * @return RenderedContentInterface Rendered HTML
     *
     * @throws \RuntimeException
     */
    public function convertToHtml(string $markdown): RenderedContentInterface
    {
        $documentAST = $this->markdownParser->parse($markdown);

        return $this->htmlRenderer->renderDocument($documentAST);
    }

    /**
     * Converts CommonMark to HTML.
     *
     * @see Converter::convertToHtml
     *
     * @throws \RuntimeException
     */
    public function __invoke(string $markdown): RenderedContentInterface
    {
        return $this->convertToHtml($markdown);
    }
}
