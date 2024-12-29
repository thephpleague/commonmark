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

namespace League\CommonMark\Tests\Functional\Extension\Autolink;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\Autolink\AutolinkExtension;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\Strikethrough\StrikethroughExtension;
use League\CommonMark\MarkdownConverter;
use PHPUnit\Framework\TestCase;

final class UrlAutolinkParserTest extends TestCase
{
    /**
     * @dataProvider dataProviderForAutolinkTests
     */
    public function testUrlAutolinks(string $input, string $expected): void
    {
        $environment = new Environment();
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new AutolinkExtension());

        $converter = new MarkdownConverter($environment);

        $this->assertEquals($expected, \trim((string) $converter->convert($input)));
    }

    /**
     * @return iterable<array<mixed>>
     */
    public static function dataProviderForAutolinkTests(): iterable
    {
        // Basic examples
        yield ['You can search on http://google.com for stuff.', '<p>You can search on <a href="http://google.com">http://google.com</a> for stuff.</p>'];
        yield ['https://google.com', '<p><a href="https://google.com">https://google.com</a></p>'];
        yield ['https://sub_domain.example.com', '<p><a href="https://sub_domain.example.com">https://sub_domain.example.com</a></p>'];
        yield ['ftp://example.com', '<p><a href="ftp://example.com">ftp://example.com</a></p>'];
        yield ['www.google.com', '<p><a href="http://www.google.com">www.google.com</a></p>'];
        yield [' http://leadingwhitespace.example.com', '<p><a href="http://leadingwhitespace.example.com">http://leadingwhitespace.example.com</a></p>'];
        yield ['http://trailingwhitespace.example.com ', '<p><a href="http://trailingwhitespace.example.com">http://trailingwhitespace.example.com</a></p>'];
        yield ['- https://example.com/list-item', "<ul>\n<li><a href=\"https://example.com/list-item\">https://example.com/list-item</a></li>\n</ul>"];

        // Tests of "incomplete" URLs
        yield ['google.com is missing www and/or a protocol', '<p>google.com is missing www and/or a protocol</p>'];
        yield ['http:/google.com is missing a slash', '<p>http:/google.com is missing a slash</p>'];
        yield ['javascript:alert(0); doesn\'t match the supported protocols', '<p>javascript:alert(0); doesn\'t match the supported protocols</p>'];

        // Tests involving trailing characters
        yield ['Maybe you\'re interested in https://www.google.com/search?q=php+commonmark!', '<p>Maybe you\'re interested in <a href="https://www.google.com/search?q=php+commonmark">https://www.google.com/search?q=php+commonmark</a>!</p>'];
        yield ['Or perhaps you\'re looking for my personal website https://www.colinodell.com...?', '<p>Or perhaps you\'re looking for my personal website <a href="https://www.colinodell.com">https://www.colinodell.com</a>...?</p>'];
        yield ['Check https://www.stackoverflow.com: they have all the answers', '<p>Check <a href="https://www.stackoverflow.com">https://www.stackoverflow.com</a>: they have all the answers</p>'];
        yield ['- https://example.com/list-item-with-trailing-colon:', "<ul>\n<li><a href=\"https://example.com/list-item-with-trailing-colon\">https://example.com/list-item-with-trailing-colon</a>:</li>\n</ul>"];
        yield ['Visit www.commonmark.org.', '<p>Visit <a href="http://www.commonmark.org">www.commonmark.org</a>.</p>'];
        yield ['Visit www.commonmark.org/a.b.', '<p>Visit <a href="http://www.commonmark.org/a.b">www.commonmark.org/a.b</a>.</p>'];

        // Tests involving parentheses
        yield ['www.google.com/search?q=Markup+(business)', '<p><a href="http://www.google.com/search?q=Markup+(business)">www.google.com/search?q=Markup+(business)</a></p>'];
        yield ['www.google.com/search?q=Markup+(business)))', '<p><a href="http://www.google.com/search?q=Markup+(business)">www.google.com/search?q=Markup+(business)</a>))</p>'];
        yield ['(www.google.com/search?q=Markup+(business))', '<p>(<a href="http://www.google.com/search?q=Markup+(business)">www.google.com/search?q=Markup+(business)</a>)</p>'];
        yield ['(www.google.com/search?q=Markup+(business)', '<p>(<a href="http://www.google.com/search?q=Markup+(business)">www.google.com/search?q=Markup+(business)</a></p>'];
        yield ['www.google.com/search?q=(business))+ok', '<p><a href="http://www.google.com/search?q=(business))+ok">www.google.com/search?q=(business))+ok</a></p>'];
        yield ['(https://www.example.com/test).', '<p>(<a href="https://www.example.com/test">https://www.example.com/test</a>).</p>'];
        yield ['WWW text followed by a [link](https://example.com/foo-bar-test)', '<p>WWW text followed by a <a href="https://example.com/foo-bar-test">link</a></p>'];

        // Tests involving semi-colon endings
        yield ['www.google.com/search?q=commonmark&hl=en', '<p><a href="http://www.google.com/search?q=commonmark&amp;hl=en">www.google.com/search?q=commonmark&amp;hl=en</a></p>'];
        yield ['www.google.com/search?q=commonmark&hl;', '<p><a href="http://www.google.com/search?q=commonmark">www.google.com/search?q=commonmark</a>&amp;hl;</p>'];

        // Test that < immediately terminates an autolink
        yield ['www.commonmark.org/he<lp', '<p><a href="http://www.commonmark.org/he">www.commonmark.org/he</a>&lt;lp</p>'];

        // Regression: two links with one underscore each
        yield ["https://eventum.example.net/history.php?iss_id=107092\nhttps://gitlab.example.net/group/project/merge_requests/39#note_150630", "<p><a href=\"https://eventum.example.net/history.php?iss_id=107092\">https://eventum.example.net/history.php?iss_id=107092</a>\n<a href=\"https://gitlab.example.net/group/project/merge_requests/39#note_150630\">https://gitlab.example.net/group/project/merge_requests/39#note_150630</a></p>"];

        // Regression: CommonMark autolinks should not be double-linked
        yield ['<https://www.google.com>', '<p><a href="https://www.google.com">https://www.google.com</a></p>'];
        yield ['[www.google.com](https://www.google.com)', '<p><a href="https://www.google.com">www.google.com</a></p>'];
        yield ['[search on **www.google.com**](https://www.google.com)', '<p><a href="https://www.google.com">search on <strong>www.google.com</strong></a></p>'];

        // Issue 492: underscores in URLs (see https://github.com/thephpleague/commonmark/issues/492)
        yield ['http://wiki/Puncutation_in_links:_why_its_bad_(and_should_be_avoided)', '<p><a href="http://wiki/Puncutation_in_links:_why_its_bad_(and_should_be_avoided)">http://wiki/Puncutation_in_links:_why_its_bad_(and_should_be_avoided)</a></p>'];
    }

    public function testUrlAutolinksWithStrikethrough(): void
    {
        $markdown = '~~Prefix i link: https://aws.amazon.com/emr/features/hadoop/~~';

        $environment = new Environment();
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new AutolinkExtension());
        $environment->addExtension(new StrikethroughExtension());

        $converter = new MarkdownConverter($environment);
        $html      = $converter->convert($markdown)->getContent();

        $this->assertSame(
            '<p><del>Prefix i link: <a href="https://aws.amazon.com/emr/features/hadoop/">https://aws.amazon.com/emr/features/hadoop/</a></del></p>' . "\n",
            $html
        );
    }

    public function testDisallowedProtocols(): void
    {
        $environment = new Environment([
            'autolink' => [
                'allowed_protocols' => ['https'],
            ],
        ]);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new AutolinkExtension());

        $converter = new MarkdownConverter($environment);
        $html      = $converter->convert('http://insecure.example.com')->getContent();

        $this->assertSame("<p>http://insecure.example.com</p>\n", $html);
    }

    /**
     * @dataProvider dataProviderForSchemes
     */
    public function testUrlAutolinksWithConfigurableSchemes(string $scheme): void
    {
        $markdown = 'www.example.com';

        $environment = new Environment([
            'autolink' => [
                'default_protocol' => $scheme,
            ],
        ]);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new AutolinkExtension());

        $converter = new MarkdownConverter($environment);
        $html      = $converter->convert($markdown)->getContent();

        $this->assertSame(
            '<p><a href="' . $scheme . '://www.example.com">www.example.com</a></p>' . "\n",
            $html
        );
    }

    /**
     * @return iterable<array<string>>
     */
    public function dataProviderForSchemes(): iterable
    {
        yield ['http'];
        yield ['https'];
        yield ['ftp'];
    }
}
