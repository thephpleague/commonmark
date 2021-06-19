<?php

declare(strict_types=1);

/*
 * This is part of the league/commonmark package.
 *
 * (c) Martin Hasoň <martin.hason@gmail.com>
 * (c) Webuni s.r.o. <info@webuni.cz>
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Tests\Functional\Extension\Table;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\Parser\MarkdownParser;
use League\CommonMark\Renderer\HtmlRenderer;
use League\CommonMark\Renderer\MarkdownRendererInterface;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class TableMarkdownTest extends TestCase
{
    private Environment $environment;

    private MarkdownParser $parser;

    protected function setUp(): void
    {
        $this->environment = new Environment();
        $this->environment->addExtension(new CommonMarkCoreExtension());
        $this->environment->addExtension(new TableExtension());

        $this->parser = new MarkdownParser($this->environment);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testRenderer(string $markdown, string $html, string $testName): void
    {
        $renderer = new HtmlRenderer($this->environment);
        $this->assertCommonMark($renderer, $markdown, $html, $testName);
    }

    /**
     * @return array<array<string>>
     */
    public function dataProvider(): array
    {
        $ret = [];
        foreach (\glob(__DIR__ . '/md/*.md') as $markdownFile) {
            $testName = \basename($markdownFile, '.md');

            $markdown = \file_get_contents($markdownFile);
            $html     = \file_get_contents(__DIR__ . '/md/' . $testName . '.html');

            $ret[] = [$markdown, $html, $testName];
        }

        return $ret;
    }

    protected function assertCommonMark(MarkdownRendererInterface $renderer, string $markdown, string $html, string $testName): void
    {
        $documentAST  = $this->parser->parse($markdown);
        $actualResult = $renderer->renderDocument($documentAST);

        $failureMessage  = \sprintf('Unexpected result for "%s" test', $testName);
        $failureMessage .= "\n=== markdown ===============\n" . $markdown;
        $failureMessage .= "\n=== expected ===============\n" . $html;
        $failureMessage .= "\n=== got ====================\n" . $actualResult;

        $this->assertEquals($html, $actualResult, $failureMessage);
    }
}
