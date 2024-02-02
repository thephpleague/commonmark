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
use League\CommonMark\MarkdownConverter;
use PHPUnit\Framework\TestCase;

final class EmailAutolinkParserTest extends TestCase
{
    /**
     * @dataProvider dataProviderForEmailAutolinks
     */
    public function testEmailAutolinks(string $input, string $expected): void
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
    public static function dataProviderForEmailAutolinks(): iterable
    {
        yield ['You can try emailing foo@example.com but that inbox doesn\'t actually exist.', '<p>You can try emailing <a href="mailto:foo@example.com">foo@example.com</a> but that inbox doesn\'t actually exist.</p>'];
        yield ['> This processor can even handle email addresses like foo@example.com inside of blockquotes!', "<blockquote>\n<p>This processor can even handle email addresses like <a href=\"mailto:foo@example.com\">foo@example.com</a> inside of blockquotes!</p>\n</blockquote>"];
        yield ['@invalid', '<p>@invalid</p>'];

        // GFM spec tests
        yield ['foo@bar.baz', '<p><a href="mailto:foo@bar.baz">foo@bar.baz</a></p>'];
        yield ['hello@mail+xyz.example isn\'t valid, but hello+xyz@mail.example is.', '<p>hello@mail+xyz.example isn\'t valid, but <a href="mailto:hello+xyz@mail.example">hello+xyz@mail.example</a> is.</p>'];
        yield ['a.b-c_d@a.b', '<p><a href="mailto:a.b-c_d@a.b">a.b-c_d@a.b</a></p>'];
        yield ['a.b-c_d@a.b.', '<p><a href="mailto:a.b-c_d@a.b">a.b-c_d@a.b</a>.</p>'];
        yield ['a.b-c_d@a.b-', '<p>a.b-c_d@a.b-</p>'];
        yield ['a.b-c_d@a.b_', '<p>a.b-c_d@a.b_</p>'];

        // Regression: CommonMark autolinks should not be double-linked
        yield ['<foo@example.com>', '<p><a href="mailto:foo@example.com">foo@example.com</a></p>'];
        yield ['[me@mydomain.com](mailto:me@mydomain.com)', '<p><a href="mailto:me@mydomain.com">me@mydomain.com</a></p>'];
        yield ['[email **me@mydomain.com**](mailto:me@mydomain.com)', '<p><a href="mailto:me@mydomain.com">email <strong>me@mydomain.com</strong></a></p>'];
    }
}
