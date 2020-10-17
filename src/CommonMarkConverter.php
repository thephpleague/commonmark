<?php

declare(strict_types=1);

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

namespace League\CommonMark;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Environment\EnvironmentInterface;
use League\CommonMark\Output\RenderedContentInterface;
use League\CommonMark\Parser\MarkdownParser;
use League\CommonMark\Parser\MarkdownParserInterface;
use League\CommonMark\Renderer\HtmlRenderer;
use League\CommonMark\Renderer\HtmlRendererInterface;

/**
 * Converts CommonMark-compatible Markdown to HTML.
 */
class CommonMarkConverter implements MarkdownConverterInterface
{
    /** @var EnvironmentInterface */
    protected $environment;

    /** @var MarkdownParserInterface */
    protected $markdownParser;

    /** @var HtmlRendererInterface */
    protected $htmlRenderer;

    /**
     * Create a new commonmark converter instance.
     *
     * @param array<string, mixed> $config
     */
    public function __construct(array $config = [], ?EnvironmentInterface $environment = null)
    {
        if ($environment === null) {
            $environment = Environment::createCommonMarkEnvironment();
        }

        if ($environment instanceof EnvironmentBuilderInterface) {
            $environment->mergeConfig($config);
        }

        $this->environment = $environment;

        $this->markdownParser = new MarkdownParser($environment);
        $this->htmlRenderer   = new HtmlRenderer($environment);
    }

    public function getEnvironment(): EnvironmentInterface
    {
        return $this->environment;
    }

    /**
     * Converts CommonMark to HTML.
     *
     * @param string $commonMark The Markdown to convert
     *
     * @return RenderedContentInterface Rendered HTML
     *
     * @throws \RuntimeException
     */
    public function convertToHtml(string $commonMark): RenderedContentInterface
    {
        $documentAST = $this->markdownParser->parse($commonMark);

        return $this->htmlRenderer->renderDocument($documentAST);
    }

    /**
     * Converts CommonMark to HTML.
     *
     * @see Converter::convertToHtml
     *
     * @throws \RuntimeException
     */
    public function __invoke(string $commonMark): RenderedContentInterface
    {
        return $this->convertToHtml($commonMark);
    }
}
