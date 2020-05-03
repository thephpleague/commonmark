<?php

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

use League\CommonMark\Environment\ConfigurableEnvironmentInterface;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Environment\EnvironmentInterface;
use League\CommonMark\Parser\MarkdownParser;
use League\CommonMark\Parser\MarkdownParserInterface;
use League\CommonMark\Renderer\HtmlRenderer;
use League\CommonMark\Renderer\HtmlRendererInterface;

/**
 * Converts CommonMark-compatible Markdown to HTML.
 */
class CommonMarkConverter implements MarkdownConverterInterface
{
    /**
     * The currently-installed version.
     *
     * This might be a typical `x.y.z` version, or `x.y-dev`.
     */
    public const VERSION = '2.0-dev';

    /** @var EnvironmentInterface */
    protected $environment;

    /** @var MarkdownParserInterface */
    protected $markdownParser;

    /** @var HtmlRendererInterface */
    protected $htmlRenderer;

    /**
     * Create a new commonmark converter instance.
     *
     * @param array<string, mixed>      $config
     * @param EnvironmentInterface|null $environment
     */
    public function __construct(array $config = [], EnvironmentInterface $environment = null)
    {
        if ($environment === null) {
            $environment = Environment::createCommonMarkEnvironment();
        }

        if ($environment instanceof ConfigurableEnvironmentInterface) {
            $environment->mergeConfig($config);
        }

        $this->environment = $environment;

        $this->markdownParser = new MarkdownParser($environment);
        $this->htmlRenderer = new HtmlRenderer($environment);
    }

    public function getEnvironment(): EnvironmentInterface
    {
        return $this->environment;
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
        $documentAST = $this->markdownParser->parse($commonMark);

        return $this->htmlRenderer->renderDocument($documentAST);
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
