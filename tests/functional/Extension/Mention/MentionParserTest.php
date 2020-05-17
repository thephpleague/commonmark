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

namespace League\CommonMark\Tests\Functional\Extension\Mention;

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\Mention\LinkGenerator\MentionLinkGeneratorInterface;
use League\CommonMark\Extension\Mention\LinkGenerator\StringTemplateLinkGenerator;
use League\CommonMark\Extension\Mention\MentionParser;
use PHPUnit\Framework\TestCase;

final class MentionParserTest extends TestCase
{
    public function testMentionParser(): void
    {
        $input    = 'See #123 for more information.';
        $expected = '<p>See <a href="https://www.example.com/123">#123</a> for more information.</p>';

        $mentionParser = new MentionParser('#', '/^\d+/', new StringTemplateLinkGenerator('https://www.example.com/%s'));

        $environment = Environment::createCommonMarkEnvironment();
        $environment->addInlineParser($mentionParser);

        $converter = new CommonMarkConverter([], $environment);

        $this->assertEquals($expected, \rtrim($converter->convertToHtml($input)));
    }

    public function testMentionParserWithoutSpaceInFront(): void
    {
        $input    = 'See#123 for more information.';
        $expected = '<p>See#123 for more information.</p>';

        $mentionParser = new MentionParser('#', '/^\d+/', $this->createMock(MentionLinkGeneratorInterface::class));

        $environment = Environment::createCommonMarkEnvironment();
        $environment->addInlineParser($mentionParser);

        $converter = new CommonMarkConverter([], $environment);

        $this->assertEquals($expected, \rtrim($converter->convertToHtml($input)));
    }

    public function testMentionParserWithNonMatchingSymbol(): void
    {
        $input    = 'See #123 for more information.';
        $expected = '<p>See #123 for more information.</p>';

        $mentionParser = new MentionParser('@', '/^\d+/', $this->createMock(MentionLinkGeneratorInterface::class));

        $environment = Environment::createCommonMarkEnvironment();
        $environment->addInlineParser($mentionParser);

        $converter = new CommonMarkConverter([], $environment);

        $this->assertEquals($expected, \rtrim($converter->convertToHtml($input)));
    }

    public function testMentionParserWithNonMatchingRegex(): void
    {
        $input    = 'See #123 for more information.';
        $expected = '<p>See #123 for more information.</p>';

        $mentionParser = new MentionParser('#', '/^[a-z]+/', $this->createMock(MentionLinkGeneratorInterface::class));

        $environment = Environment::createCommonMarkEnvironment();
        $environment->addInlineParser($mentionParser);

        $converter = new CommonMarkConverter([], $environment);

        $this->assertEquals($expected, \rtrim($converter->convertToHtml($input)));
    }

    public function testMentionParserWithNullUrl(): void
    {
        $input    = 'See #123 for more information.';
        $expected = '<p>See #123 for more information.</p>';

        $returnsNull = $this->createMock(MentionLinkGeneratorInterface::class);
        $returnsNull->method('generateLink')->willReturn(null);

        $mentionParser = new MentionParser('#', '/^\d+/', $returnsNull);

        $environment = Environment::createCommonMarkEnvironment();
        $environment->addInlineParser($mentionParser);

        $converter = new CommonMarkConverter([], $environment);

        $this->assertEquals($expected, \rtrim($converter->convertToHtml($input)));
    }

    public function testMentionParserUsingCallback(): void
    {
        $callable = static function ($handle, &$label, $symbol) {
            // Stuff the three params into the URL just to prove we received them all properly
            $url = \sprintf('https://www.example.com/%s/%s/%s', $handle, $label, $symbol);

            // Change the label (by reference)
            $label = 'Replaced Label';

            return $url;
        };

        $input    = 'This should parse #123.';
        $expected = '<p>This should parse <a href="https://www.example.com/123/#123/#">Replaced Label</a>.</p>';

        $mentionParser = MentionParser::createWithCallback('#', '/^\d+/', $callable);

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
