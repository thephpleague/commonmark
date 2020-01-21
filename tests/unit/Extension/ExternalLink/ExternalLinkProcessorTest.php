<?php

/*
 * This file is part of the league/commonmark-ext-external-link package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Tests\Extension\ExternalLink;

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;
use League\CommonMark\Extension\ExternalLink\ExternalLinkExtension;
use League\CommonMark\Extension\ExternalLink\ExternalLinkProcessor;
use PHPUnit\Framework\TestCase;

final class ExternalLinkProcessorTest extends TestCase
{
    private const INPUT = 'My favorite sites are <https://www.colinodell.com> and <https://commonmark.thephpleague.com>';

    public function testDefaultConfiguration()
    {
        $expected = '<p>My favorite sites are <a rel="noopener noreferrer" href="https://www.colinodell.com">https://www.colinodell.com</a> and <a rel="noopener noreferrer" href="https://commonmark.thephpleague.com">https://commonmark.thephpleague.com</a></p>' . "\n";

        $this->assertEquals($expected, $this->parse(self::INPUT));
    }

    public function testCustomConfiguration()
    {
        $expected = '<p>My favorite sites are <a rel="noopener noreferrer" target="_blank" class="external-link" href="https://www.colinodell.com">https://www.colinodell.com</a> and <a href="https://commonmark.thephpleague.com">https://commonmark.thephpleague.com</a></p>' . "\n";

        $config = [
            'external_link' => [
                'internal_hosts'     => ['commonmark.thephpleague.com'],
                'open_in_new_window' => true,
                'html_class'         => 'external-link',
            ],
        ];

        $this->assertEquals($expected, $this->parse(self::INPUT, $config));
    }

    public function testWithBadUrls()
    {
        $input = 'Report [xss](javascript:alert(0);) vulnerabilities by emailing <colinodell@gmail.com>';

        $expected = '<p>Report <a href="javascript:alert(0);">xss</a> vulnerabilities by emailing <a href="mailto:colinodell@gmail.com">colinodell@gmail.com</a></p>' . "\n";

        $this->assertEquals($expected, $this->parse($input));
    }

    private function parse(string $markdown, array $config = [])
    {
        $e = Environment::createCommonMarkEnvironment();
        $e->addExtension(new ExternalLinkExtension());

        $c = new CommonMarkConverter($config, $e);

        return $c->convertToHtml($markdown);
    }

    /**
     * @param string $host
     * @param mixed  $compareTo
     * @param bool   $expected
     *
     * @dataProvider dataProviderForTestHostMatches
     */
    public function testHostMatches(string $host, $compareTo, bool $expected)
    {
        $this->assertEquals($expected, ExternalLinkProcessor::hostMatches($host, $compareTo));
    }

    public function dataProviderForTestHostMatches()
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
}
