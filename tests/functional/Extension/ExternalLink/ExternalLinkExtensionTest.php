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

namespace League\CommonMark\Tests\Functional\Extension\ExternalLink;

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Environment\EnvironmentInterface;
use League\CommonMark\Extension\Autolink\AutolinkExtension;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\ExternalLink\ExternalLinkExtension;
use League\CommonMark\Parser\MarkdownParser;
use League\CommonMark\Renderer\HtmlRenderer;
use PHPUnit\Framework\TestCase;

final class ExternalLinkExtensionTest extends TestCase
{
    /**
     * @dataProvider provideEnvironmentForTestingExtensionWithAutolinks
     */
    public function testExtensionWithAutolinks(EnvironmentInterface $environment): void
    {
        $markdown     = 'Email me at colinodell@gmail.com or read the docs at https://commonmark.thephpleague.com';
        $expectedHtml = '<p>Email me at <a href="mailto:colinodell@gmail.com">colinodell@gmail.com</a> or read the docs at <a rel="noopener noreferrer" class="external-link" href="https://commonmark.thephpleague.com">https://commonmark.thephpleague.com</a></p>';

        $parser   = new MarkdownParser($environment);
        $renderer = new HtmlRenderer($environment);

        $this->assertSame($expectedHtml, \rtrim($renderer->renderDocument($parser->parse($markdown))->getContent()));
    }

    /**
     * @return iterable<mixed>
     */
    public static function provideEnvironmentForTestingExtensionWithAutolinks(): iterable
    {
        $environment1 = new Environment([
            'external_link' => [
                'html_class' => 'external-link',
            ],
        ]);
        $environment1->addExtension(new CommonMarkCoreExtension());
        $environment1->addExtension(new ExternalLinkExtension());
        $environment1->addExtension(new AutolinkExtension());

        yield 'Register ExternalLink extension first' => [$environment1];

        $environment2 = new Environment([
            'external_link' => [
                'html_class' => 'external-link',
            ],
        ]);
        $environment2->addExtension(new CommonMarkCoreExtension());
        $environment2->addExtension(new AutolinkExtension());
        $environment2->addExtension(new ExternalLinkExtension());

        yield 'Register Autolink extension first' => [$environment2];
    }

    public function testExtensionWithRelAttrsDisabled(): void
    {
        $config = [
            'external_link' => [
                'internal_hosts' => ['my-internal-domain.com'],
                'open_in_new_window' => true,
                'nofollow' => '',
                'noopener' => '',
                'noreferrer' => '',
            ],
        ];

        $converter = new CommonMarkConverter($config);
        $converter->getEnvironment()->addExtension(new ExternalLinkExtension());

        $input        = 'This is an external link [Google](https://google.com/).';
        $expectedHtml = '<p>This is an external link <a target="_blank" href="https://google.com/">Google</a>.</p>';

        $this->assertSame($expectedHtml, \rtrim($converter->convert($input)->getContent()));
    }
}
