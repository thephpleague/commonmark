<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Tests\Unit\Extension\Mention;

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;
use League\CommonMark\Extension\Mention\MentionParser;
use PHPUnit\Framework\TestCase;

final class MentionParserTest extends TestCase
{
    public function testMentionParser(): void
    {
        $input = 'See #123 for more information.';
        $expected = '<p>See <a href="https://www.example.com/123">#123</a> for more information.</p>';

        $mentionParser = new MentionParser('#', '/^\d+/', function (string $mention) { return 'https://www.example.com/' . $mention; });

        $environment = Environment::createCommonMarkEnvironment();
        $environment->addInlineParser($mentionParser);

        $converter = new CommonMarkConverter([], $environment);

        $this->assertEquals($expected, \rtrim($converter->convertToHtml($input)));
    }

    public function testMentionParserWithoutSpaceInFront(): void
    {
        $input = 'See#123 for more information.';
        $expected = '<p>See#123 for more information.</p>';

        $mentionParser = new MentionParser('#', '/^\d+/', 'trim');

        $environment = Environment::createCommonMarkEnvironment();
        $environment->addInlineParser($mentionParser);

        $converter = new CommonMarkConverter([], $environment);

        $this->assertEquals($expected, \rtrim($converter->convertToHtml($input)));
    }

    public function testMentionParserWithNonMatchingSymbol(): void
    {
        $input = 'See #123 for more information.';
        $expected = '<p>See #123 for more information.</p>';

        $mentionParser = new MentionParser('@', '/^\d+/', 'trim');

        $environment = Environment::createCommonMarkEnvironment();
        $environment->addInlineParser($mentionParser);

        $converter = new CommonMarkConverter([], $environment);

        $this->assertEquals($expected, \rtrim($converter->convertToHtml($input)));
    }

    public function testMentionParserWithNonMatchingRegex(): void
    {
        $input = 'See #123 for more information.';
        $expected = '<p>See #123 for more information.</p>';

        $mentionParser = new MentionParser('#', '/^[a-z]+/', 'trim');

        $environment = Environment::createCommonMarkEnvironment();
        $environment->addInlineParser($mentionParser);

        $converter = new CommonMarkConverter([], $environment);

        $this->assertEquals($expected, \rtrim($converter->convertToHtml($input)));
    }

    public function testMentionParserWithNullUrl(): void
    {
        $input = 'See #123 for more information.';
        $expected = '<p>See #123 for more information.</p>';

        $mentionParser = new MentionParser('#', '/^\d+/', function (string $mention) { return null; });

        $environment = Environment::createCommonMarkEnvironment();
        $environment->addInlineParser($mentionParser);

        $converter = new CommonMarkConverter([], $environment);

        $this->assertEquals($expected, \rtrim($converter->convertToHtml($input)));
    }

    public function testMentionParserThatReturnsLinksFromCallable(): void
    {
        $callable = function (string $mention, string $symbol) {
            return "[called with $mention and $symbol]";
        };

        $input = 'This should parse #123.';
        $expected = '<p>This should parse <a href="[called with 123 and #]">#123</a>.</p>';

        $mentionParser = new MentionParser('#', '/^\d+/', $callable);

        $environment = Environment::createCommonMarkEnvironment();
        $environment->addInlineParser($mentionParser);

        $converter = new CommonMarkConverter([], $environment);

        $this->assertEquals($expected, \rtrim($converter->convertToHtml($input)));
    }

    public function testTwitterMentionParser(): void
    {
        $input = <<<'EOT'
You can follow the author of this library on Twitter - he's @colinodell!

Usernames like @commonmarkisthebestmarkdownspec are too long.

Security issues should be emailed to colinodell@gmail.com
EOT;

        $expected = <<<'EOT'
<p>You can follow the author of this library on Twitter - he's <a href="https://twitter.com/colinodell">@colinodell</a>!</p>
<p>Usernames like @commonmarkisthebestmarkdownspec are too long.</p>
<p>Security issues should be emailed to colinodell@gmail.com</p>

EOT;

        $environment = Environment::createCommonMarkEnvironment();
        $environment->addInlineParser(MentionParser::createTwitterHandleParser());

        $converter = new CommonMarkConverter([], $environment);

        $this->assertEquals($expected, $converter->convertToHtml($input));
    }

    public function testGithubMentionParser(): void
    {
        $input = <<<'EOT'
You can follow the author of this library on Github - he's @colinodell!
EOT;

        $expected = <<<'EOT'
<p>You can follow the author of this library on Github - he's <a href="https://github.com/colinodell">@colinodell</a>!</p>

EOT;

        $environment = Environment::createCommonMarkEnvironment();
        $environment->addInlineParser(MentionParser::createGitHubHandleParser());

        $converter = new CommonMarkConverter([], $environment);

        $this->assertEquals($expected, $converter->convertToHtml($input));
    }

    public function testGithubIssueParser(): void
    {
        $input = <<<'EOT'
This feature was implemented thanks to #473 by Mark Carver.
EOT;

        $expected = <<<'EOT'
<p>This feature was implemented thanks to <a href="https://github.com/thephpleague/commonmark/issues/473">#473</a> by Mark Carver.</p>

EOT;

        $environment = Environment::createCommonMarkEnvironment();
        $environment->addInlineParser(MentionParser::createGitHubIssueParser('thephpleague/commonmark'));

        $converter = new CommonMarkConverter([], $environment);

        $this->assertEquals($expected, $converter->convertToHtml($input));
    }
}
