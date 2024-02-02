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

namespace League\CommonMark\Tests\Unit\Extension\ExternalLink;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\ExternalLink\ExternalLinkExtension;
use League\CommonMark\Extension\ExternalLink\ExternalLinkProcessor;
use League\CommonMark\MarkdownConverter;
use PHPUnit\Framework\TestCase;

final class ExternalLinkProcessorTest extends TestCase
{
    private const INPUT = 'My favorite sites are <https://www.colinodell.com> and <https://commonmark.thephpleague.com>';

    public function testDefaultConfiguration(): void
    {
        $expected = '<p>My favorite sites are <a rel="noopener noreferrer" href="https://www.colinodell.com">https://www.colinodell.com</a> and <a rel="noopener noreferrer" href="https://commonmark.thephpleague.com">https://commonmark.thephpleague.com</a></p>';

        $this->assertEquals($expected, $this->parse(self::INPUT));
    }

    public function testCustomConfiguration(): void
    {
        $expected = '<p>My favorite sites are <a rel="noopener noreferrer" target="_blank" class="external-link" href="https://www.colinodell.com">https://www.colinodell.com</a> and <a href="https://commonmark.thephpleague.com">https://commonmark.thephpleague.com</a></p>';

        $config = [
            'external_link' => [
                'internal_hosts'     => ['commonmark.thephpleague.com'],
                'open_in_new_window' => true,
                'html_class'         => 'external-link',
            ],
        ];

        $this->assertEquals($expected, $this->parse(self::INPUT, $config));
    }

    public function testWithBadUrls(): void
    {
        $input = 'Report [xss](javascript:alert(0);) vulnerabilities by emailing <colinodell@gmail.com>';

        $expected = '<p>Report <a href="javascript:alert(0);">xss</a> vulnerabilities by emailing <a href="mailto:colinodell@gmail.com">colinodell@gmail.com</a></p>';

        $this->assertEquals($expected, $this->parse($input));
    }

    /**
     * @param array<string, mixed> $config
     */
    private function parse(string $markdown, array $config = []): string
    {
        $e = new Environment($config);
        $e->addExtension(new CommonMarkCoreExtension());
        $e->addExtension(new ExternalLinkExtension());

        $c = new MarkdownConverter($e);

        return \rtrim((string) $c->convert($markdown));
    }

    /**
     * @dataProvider dataProviderForTestHostMatches
     *
     * @param mixed $compareTo
     */
    public function testHostMatches(string $host, $compareTo, bool $expected): void
    {
        $this->assertEquals($expected, ExternalLinkProcessor::hostMatches($host, $compareTo));
    }

    /**
     * @return iterable<array<string|bool>>
     */
    public static function dataProviderForTestHostMatches(): iterable
    {
        // String-to-string comparison must match exactly
        yield ['colinodell.com', 'commonmark.thephpleague.com', false];
        yield ['colinodell.com', 'colinodell.com', true];

        // Subdomains won't match unless using regex
        yield ['www.colinodell.com', 'colinodell.com', false];
        yield ['www.colinodell.com', '/colinodell\.com/', true];

        // Multiple strings can be checked
        yield ['www.colinodell.com', ['www.colinodell.com', 'commonmark.thephpleague.com'], true];
        yield ['www.colinodell.com', ['google.com', 'aol.com'], false];

        // You can even mix-and-match multiple strings with multiple regexes
        yield ['www.colinodell.com', ['/colinodell\.com/', 'aol.com'], true];
    }

    /**
     * @dataProvider dataProviderForTestRelOptions
     */
    public function testRelOptions(string $nofollow, string $noopener, string $noreferrer, string $expectedOutput): void
    {
        $config = [
            'external_link' => [
                'nofollow'       => $nofollow,
                'noopener'       => $noopener,
                'noreferrer'     => $noreferrer,
                'internal_hosts' => 'commonmark.thephpleague.com',
            ],
        ];

        $this->assertEquals($expectedOutput, $this->parse(self::INPUT, $config));
    }

    /**
     * @return iterable<string[]>
     */
    public static function dataProviderForTestRelOptions(): iterable
    {
        // phpcs:disable SlevomatCodingStandard.Arrays.SingleLineArrayWhitespace.SpaceAfterComma
        yield ['',         '',         '',         '<p>My favorite sites are <a href="https://www.colinodell.com">https://www.colinodell.com</a> and <a href="https://commonmark.thephpleague.com">https://commonmark.thephpleague.com</a></p>'];
        yield ['',         '',         'all',      '<p>My favorite sites are <a rel="noreferrer" href="https://www.colinodell.com">https://www.colinodell.com</a> and <a rel="noreferrer" href="https://commonmark.thephpleague.com">https://commonmark.thephpleague.com</a></p>'];
        yield ['',         '',         'external', '<p>My favorite sites are <a rel="noreferrer" href="https://www.colinodell.com">https://www.colinodell.com</a> and <a href="https://commonmark.thephpleague.com">https://commonmark.thephpleague.com</a></p>'];
        yield ['',         '',         'internal', '<p>My favorite sites are <a href="https://www.colinodell.com">https://www.colinodell.com</a> and <a rel="noreferrer" href="https://commonmark.thephpleague.com">https://commonmark.thephpleague.com</a></p>'];
        yield ['',         'all',      '',         '<p>My favorite sites are <a rel="noopener" href="https://www.colinodell.com">https://www.colinodell.com</a> and <a rel="noopener" href="https://commonmark.thephpleague.com">https://commonmark.thephpleague.com</a></p>'];
        yield ['',         'all',      'all',      '<p>My favorite sites are <a rel="noopener noreferrer" href="https://www.colinodell.com">https://www.colinodell.com</a> and <a rel="noopener noreferrer" href="https://commonmark.thephpleague.com">https://commonmark.thephpleague.com</a></p>'];
        yield ['',         'all',      'external', '<p>My favorite sites are <a rel="noopener noreferrer" href="https://www.colinodell.com">https://www.colinodell.com</a> and <a rel="noopener" href="https://commonmark.thephpleague.com">https://commonmark.thephpleague.com</a></p>'];
        yield ['',         'all',      'internal', '<p>My favorite sites are <a rel="noopener" href="https://www.colinodell.com">https://www.colinodell.com</a> and <a rel="noopener noreferrer" href="https://commonmark.thephpleague.com">https://commonmark.thephpleague.com</a></p>'];
        yield ['',         'external', '',         '<p>My favorite sites are <a rel="noopener" href="https://www.colinodell.com">https://www.colinodell.com</a> and <a href="https://commonmark.thephpleague.com">https://commonmark.thephpleague.com</a></p>'];
        yield ['',         'external', 'all',      '<p>My favorite sites are <a rel="noopener noreferrer" href="https://www.colinodell.com">https://www.colinodell.com</a> and <a rel="noreferrer" href="https://commonmark.thephpleague.com">https://commonmark.thephpleague.com</a></p>'];
        yield ['',         'external', 'external', '<p>My favorite sites are <a rel="noopener noreferrer" href="https://www.colinodell.com">https://www.colinodell.com</a> and <a href="https://commonmark.thephpleague.com">https://commonmark.thephpleague.com</a></p>'];
        yield ['',         'external', 'internal', '<p>My favorite sites are <a rel="noopener" href="https://www.colinodell.com">https://www.colinodell.com</a> and <a rel="noreferrer" href="https://commonmark.thephpleague.com">https://commonmark.thephpleague.com</a></p>'];
        yield ['',         'internal', '',         '<p>My favorite sites are <a href="https://www.colinodell.com">https://www.colinodell.com</a> and <a rel="noopener" href="https://commonmark.thephpleague.com">https://commonmark.thephpleague.com</a></p>'];
        yield ['',         'internal', 'all',      '<p>My favorite sites are <a rel="noreferrer" href="https://www.colinodell.com">https://www.colinodell.com</a> and <a rel="noopener noreferrer" href="https://commonmark.thephpleague.com">https://commonmark.thephpleague.com</a></p>'];
        yield ['',         'internal', 'external', '<p>My favorite sites are <a rel="noreferrer" href="https://www.colinodell.com">https://www.colinodell.com</a> and <a rel="noopener" href="https://commonmark.thephpleague.com">https://commonmark.thephpleague.com</a></p>'];
        yield ['',         'internal', 'internal', '<p>My favorite sites are <a href="https://www.colinodell.com">https://www.colinodell.com</a> and <a rel="noopener noreferrer" href="https://commonmark.thephpleague.com">https://commonmark.thephpleague.com</a></p>'];
        yield ['all',      '',         '',         '<p>My favorite sites are <a rel="nofollow" href="https://www.colinodell.com">https://www.colinodell.com</a> and <a rel="nofollow" href="https://commonmark.thephpleague.com">https://commonmark.thephpleague.com</a></p>'];
        yield ['all',      '',         'all',      '<p>My favorite sites are <a rel="nofollow noreferrer" href="https://www.colinodell.com">https://www.colinodell.com</a> and <a rel="nofollow noreferrer" href="https://commonmark.thephpleague.com">https://commonmark.thephpleague.com</a></p>'];
        yield ['all',      '',         'external', '<p>My favorite sites are <a rel="nofollow noreferrer" href="https://www.colinodell.com">https://www.colinodell.com</a> and <a rel="nofollow" href="https://commonmark.thephpleague.com">https://commonmark.thephpleague.com</a></p>'];
        yield ['all',      '',         'internal', '<p>My favorite sites are <a rel="nofollow" href="https://www.colinodell.com">https://www.colinodell.com</a> and <a rel="nofollow noreferrer" href="https://commonmark.thephpleague.com">https://commonmark.thephpleague.com</a></p>'];
        yield ['all',      'all',      '',         '<p>My favorite sites are <a rel="nofollow noopener" href="https://www.colinodell.com">https://www.colinodell.com</a> and <a rel="nofollow noopener" href="https://commonmark.thephpleague.com">https://commonmark.thephpleague.com</a></p>'];
        yield ['all',      'all',      'all',      '<p>My favorite sites are <a rel="nofollow noopener noreferrer" href="https://www.colinodell.com">https://www.colinodell.com</a> and <a rel="nofollow noopener noreferrer" href="https://commonmark.thephpleague.com">https://commonmark.thephpleague.com</a></p>'];
        yield ['all',      'all',      'external', '<p>My favorite sites are <a rel="nofollow noopener noreferrer" href="https://www.colinodell.com">https://www.colinodell.com</a> and <a rel="nofollow noopener" href="https://commonmark.thephpleague.com">https://commonmark.thephpleague.com</a></p>'];
        yield ['all',      'all',      'internal', '<p>My favorite sites are <a rel="nofollow noopener" href="https://www.colinodell.com">https://www.colinodell.com</a> and <a rel="nofollow noopener noreferrer" href="https://commonmark.thephpleague.com">https://commonmark.thephpleague.com</a></p>'];
        yield ['all',      'external', '',         '<p>My favorite sites are <a rel="nofollow noopener" href="https://www.colinodell.com">https://www.colinodell.com</a> and <a rel="nofollow" href="https://commonmark.thephpleague.com">https://commonmark.thephpleague.com</a></p>'];
        yield ['all',      'external', 'all',      '<p>My favorite sites are <a rel="nofollow noopener noreferrer" href="https://www.colinodell.com">https://www.colinodell.com</a> and <a rel="nofollow noreferrer" href="https://commonmark.thephpleague.com">https://commonmark.thephpleague.com</a></p>'];
        yield ['all',      'external', 'external', '<p>My favorite sites are <a rel="nofollow noopener noreferrer" href="https://www.colinodell.com">https://www.colinodell.com</a> and <a rel="nofollow" href="https://commonmark.thephpleague.com">https://commonmark.thephpleague.com</a></p>'];
        yield ['all',      'external', 'internal', '<p>My favorite sites are <a rel="nofollow noopener" href="https://www.colinodell.com">https://www.colinodell.com</a> and <a rel="nofollow noreferrer" href="https://commonmark.thephpleague.com">https://commonmark.thephpleague.com</a></p>'];
        yield ['all',      'internal', '',         '<p>My favorite sites are <a rel="nofollow" href="https://www.colinodell.com">https://www.colinodell.com</a> and <a rel="nofollow noopener" href="https://commonmark.thephpleague.com">https://commonmark.thephpleague.com</a></p>'];
        yield ['all',      'internal', 'all',      '<p>My favorite sites are <a rel="nofollow noreferrer" href="https://www.colinodell.com">https://www.colinodell.com</a> and <a rel="nofollow noopener noreferrer" href="https://commonmark.thephpleague.com">https://commonmark.thephpleague.com</a></p>'];
        yield ['all',      'internal', 'external', '<p>My favorite sites are <a rel="nofollow noreferrer" href="https://www.colinodell.com">https://www.colinodell.com</a> and <a rel="nofollow noopener" href="https://commonmark.thephpleague.com">https://commonmark.thephpleague.com</a></p>'];
        yield ['all',      'internal', 'internal', '<p>My favorite sites are <a rel="nofollow" href="https://www.colinodell.com">https://www.colinodell.com</a> and <a rel="nofollow noopener noreferrer" href="https://commonmark.thephpleague.com">https://commonmark.thephpleague.com</a></p>'];
        yield ['external', '',         '',         '<p>My favorite sites are <a rel="nofollow" href="https://www.colinodell.com">https://www.colinodell.com</a> and <a href="https://commonmark.thephpleague.com">https://commonmark.thephpleague.com</a></p>'];
        yield ['external', '',         'all',      '<p>My favorite sites are <a rel="nofollow noreferrer" href="https://www.colinodell.com">https://www.colinodell.com</a> and <a rel="noreferrer" href="https://commonmark.thephpleague.com">https://commonmark.thephpleague.com</a></p>'];
        yield ['external', '',         'external', '<p>My favorite sites are <a rel="nofollow noreferrer" href="https://www.colinodell.com">https://www.colinodell.com</a> and <a href="https://commonmark.thephpleague.com">https://commonmark.thephpleague.com</a></p>'];
        yield ['external', '',         'internal', '<p>My favorite sites are <a rel="nofollow" href="https://www.colinodell.com">https://www.colinodell.com</a> and <a rel="noreferrer" href="https://commonmark.thephpleague.com">https://commonmark.thephpleague.com</a></p>'];
        yield ['external', 'all',      '',         '<p>My favorite sites are <a rel="nofollow noopener" href="https://www.colinodell.com">https://www.colinodell.com</a> and <a rel="noopener" href="https://commonmark.thephpleague.com">https://commonmark.thephpleague.com</a></p>'];
        yield ['external', 'all',      'all',      '<p>My favorite sites are <a rel="nofollow noopener noreferrer" href="https://www.colinodell.com">https://www.colinodell.com</a> and <a rel="noopener noreferrer" href="https://commonmark.thephpleague.com">https://commonmark.thephpleague.com</a></p>'];
        yield ['external', 'all',      'external', '<p>My favorite sites are <a rel="nofollow noopener noreferrer" href="https://www.colinodell.com">https://www.colinodell.com</a> and <a rel="noopener" href="https://commonmark.thephpleague.com">https://commonmark.thephpleague.com</a></p>'];
        yield ['external', 'all',      'internal', '<p>My favorite sites are <a rel="nofollow noopener" href="https://www.colinodell.com">https://www.colinodell.com</a> and <a rel="noopener noreferrer" href="https://commonmark.thephpleague.com">https://commonmark.thephpleague.com</a></p>'];
        yield ['external', 'external', '',         '<p>My favorite sites are <a rel="nofollow noopener" href="https://www.colinodell.com">https://www.colinodell.com</a> and <a href="https://commonmark.thephpleague.com">https://commonmark.thephpleague.com</a></p>'];
        yield ['external', 'external', 'all',      '<p>My favorite sites are <a rel="nofollow noopener noreferrer" href="https://www.colinodell.com">https://www.colinodell.com</a> and <a rel="noreferrer" href="https://commonmark.thephpleague.com">https://commonmark.thephpleague.com</a></p>'];
        yield ['external', 'external', 'external', '<p>My favorite sites are <a rel="nofollow noopener noreferrer" href="https://www.colinodell.com">https://www.colinodell.com</a> and <a href="https://commonmark.thephpleague.com">https://commonmark.thephpleague.com</a></p>'];
        yield ['external', 'external', 'internal', '<p>My favorite sites are <a rel="nofollow noopener" href="https://www.colinodell.com">https://www.colinodell.com</a> and <a rel="noreferrer" href="https://commonmark.thephpleague.com">https://commonmark.thephpleague.com</a></p>'];
        yield ['external', 'internal', '',         '<p>My favorite sites are <a rel="nofollow" href="https://www.colinodell.com">https://www.colinodell.com</a> and <a rel="noopener" href="https://commonmark.thephpleague.com">https://commonmark.thephpleague.com</a></p>'];
        yield ['external', 'internal', 'all',      '<p>My favorite sites are <a rel="nofollow noreferrer" href="https://www.colinodell.com">https://www.colinodell.com</a> and <a rel="noopener noreferrer" href="https://commonmark.thephpleague.com">https://commonmark.thephpleague.com</a></p>'];
        yield ['external', 'internal', 'external', '<p>My favorite sites are <a rel="nofollow noreferrer" href="https://www.colinodell.com">https://www.colinodell.com</a> and <a rel="noopener" href="https://commonmark.thephpleague.com">https://commonmark.thephpleague.com</a></p>'];
        yield ['external', 'internal', 'internal', '<p>My favorite sites are <a rel="nofollow" href="https://www.colinodell.com">https://www.colinodell.com</a> and <a rel="noopener noreferrer" href="https://commonmark.thephpleague.com">https://commonmark.thephpleague.com</a></p>'];
        yield ['internal', '',         '',         '<p>My favorite sites are <a href="https://www.colinodell.com">https://www.colinodell.com</a> and <a rel="nofollow" href="https://commonmark.thephpleague.com">https://commonmark.thephpleague.com</a></p>'];
        yield ['internal', '',         'all',      '<p>My favorite sites are <a rel="noreferrer" href="https://www.colinodell.com">https://www.colinodell.com</a> and <a rel="nofollow noreferrer" href="https://commonmark.thephpleague.com">https://commonmark.thephpleague.com</a></p>'];
        yield ['internal', '',         'external', '<p>My favorite sites are <a rel="noreferrer" href="https://www.colinodell.com">https://www.colinodell.com</a> and <a rel="nofollow" href="https://commonmark.thephpleague.com">https://commonmark.thephpleague.com</a></p>'];
        yield ['internal', '',         'internal', '<p>My favorite sites are <a href="https://www.colinodell.com">https://www.colinodell.com</a> and <a rel="nofollow noreferrer" href="https://commonmark.thephpleague.com">https://commonmark.thephpleague.com</a></p>'];
        yield ['internal', 'all',      '',         '<p>My favorite sites are <a rel="noopener" href="https://www.colinodell.com">https://www.colinodell.com</a> and <a rel="nofollow noopener" href="https://commonmark.thephpleague.com">https://commonmark.thephpleague.com</a></p>'];
        yield ['internal', 'all',      'all',      '<p>My favorite sites are <a rel="noopener noreferrer" href="https://www.colinodell.com">https://www.colinodell.com</a> and <a rel="nofollow noopener noreferrer" href="https://commonmark.thephpleague.com">https://commonmark.thephpleague.com</a></p>'];
        yield ['internal', 'all',      'external', '<p>My favorite sites are <a rel="noopener noreferrer" href="https://www.colinodell.com">https://www.colinodell.com</a> and <a rel="nofollow noopener" href="https://commonmark.thephpleague.com">https://commonmark.thephpleague.com</a></p>'];
        yield ['internal', 'all',      'internal', '<p>My favorite sites are <a rel="noopener" href="https://www.colinodell.com">https://www.colinodell.com</a> and <a rel="nofollow noopener noreferrer" href="https://commonmark.thephpleague.com">https://commonmark.thephpleague.com</a></p>'];
        yield ['internal', 'external', '',         '<p>My favorite sites are <a rel="noopener" href="https://www.colinodell.com">https://www.colinodell.com</a> and <a rel="nofollow" href="https://commonmark.thephpleague.com">https://commonmark.thephpleague.com</a></p>'];
        yield ['internal', 'external', 'all',      '<p>My favorite sites are <a rel="noopener noreferrer" href="https://www.colinodell.com">https://www.colinodell.com</a> and <a rel="nofollow noreferrer" href="https://commonmark.thephpleague.com">https://commonmark.thephpleague.com</a></p>'];
        yield ['internal', 'external', 'external', '<p>My favorite sites are <a rel="noopener noreferrer" href="https://www.colinodell.com">https://www.colinodell.com</a> and <a rel="nofollow" href="https://commonmark.thephpleague.com">https://commonmark.thephpleague.com</a></p>'];
        yield ['internal', 'external', 'internal', '<p>My favorite sites are <a rel="noopener" href="https://www.colinodell.com">https://www.colinodell.com</a> and <a rel="nofollow noreferrer" href="https://commonmark.thephpleague.com">https://commonmark.thephpleague.com</a></p>'];
        yield ['internal', 'internal', '',         '<p>My favorite sites are <a href="https://www.colinodell.com">https://www.colinodell.com</a> and <a rel="nofollow noopener" href="https://commonmark.thephpleague.com">https://commonmark.thephpleague.com</a></p>'];
        yield ['internal', 'internal', 'all',      '<p>My favorite sites are <a rel="noreferrer" href="https://www.colinodell.com">https://www.colinodell.com</a> and <a rel="nofollow noopener noreferrer" href="https://commonmark.thephpleague.com">https://commonmark.thephpleague.com</a></p>'];
        yield ['internal', 'internal', 'external', '<p>My favorite sites are <a rel="noreferrer" href="https://www.colinodell.com">https://www.colinodell.com</a> and <a rel="nofollow noopener" href="https://commonmark.thephpleague.com">https://commonmark.thephpleague.com</a></p>'];
        yield ['internal', 'internal', 'internal', '<p>My favorite sites are <a href="https://www.colinodell.com">https://www.colinodell.com</a> and <a rel="nofollow noopener noreferrer" href="https://commonmark.thephpleague.com">https://commonmark.thephpleague.com</a></p>'];
    }
}
