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

namespace League\CommonMark\Extension\FrontMatter;

use League\CommonMark\Environment\ConfigurableEnvironmentInterface;
use League\CommonMark\Event\DocumentPreParsedEvent;
use League\CommonMark\Event\DocumentRenderedEvent;
use League\CommonMark\Extension\ExtensionInterface;
use League\CommonMark\Extension\FrontMatter\Yaml\FrontMatterParserInterface;
use League\CommonMark\Extension\FrontMatter\Yaml\SymfonyFrontMatterParser;

final class FrontMatterExtension implements ExtensionInterface
{
    /**
     * @var FrontMatterParserInterface
     *
     * @psalm-readonly
     */
    private $frontMatterParser;

    public function __construct(?FrontMatterParserInterface $frontMatterParser = null)
    {
        $this->frontMatterParser = $frontMatterParser ?? new SymfonyFrontMatterParser();
    }

    public function register(ConfigurableEnvironmentInterface $environment): void
    {
        $environment->addEventListener(DocumentPreParsedEvent::class, new FrontMatterParserListener($this->frontMatterParser));
        $environment->addEventListener(DocumentRenderedEvent::class, new FrontMatterRenderListener(), -500);
    }
}
