<?php

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
use League\CommonMark\Environment;
use League\CommonMark\Extension\Mention\Generator\MentionGeneratorInterface;
use League\CommonMark\Extension\Mention\Generator\StringTemplateLinkGenerator;
use League\CommonMark\Extension\Mention\Mention;
use League\CommonMark\Extension\Mention\MentionParser;
use League\CommonMark\Inline\Element\Emphasis;
use League\CommonMark\Inline\Element\Text;
use PHPUnit\Framework\TestCase;

final class MentionParserTest extends TestCase
{
    public function testMentionParser(): void
    {
        $input = 'See #123 for more information.';
        $expected = '<p>See <a href="https://www.example.com/123">#123</a> for more information.</p>';

        $mentionParser = new MentionParser('#', '/^\d+/', new StringTemplateLinkGenerator('https://www.example.com/%s'));

        $environment = Environment::createCommonMarkEnvironment();
        $environment->addInlineParser($mentionParser);

        $converter = new CommonMarkConverter([], $environment);

        $this->assertEquals($expected, \rtrim($converter->convertToHtml($input)));
    }

    public function testMentionParserWithoutSpaceInFront(): void
    {
        $input = 'See#123 for more information.';
        $expected = '<p>See#123 for more information.</p>';

        $mentionParser = new MentionParser('#', '/^\d+/', $this->createMock(MentionGeneratorInterface::class));

        $environment = Environment::createCommonMarkEnvironment();
        $environment->addInlineParser($mentionParser);

        $converter = new CommonMarkConverter([], $environment);

        $this->assertEquals($expected, \rtrim($converter->convertToHtml($input)));
    }

    public function testMentionParserWithNonMatchingSymbol(): void
    {
        $input = 'See #123 for more information.';
        $expected = '<p>See #123 for more information.</p>';

        $mentionParser = new MentionParser('@', '/^\d+/', $this->createMock(MentionGeneratorInterface::class));

        $environment = Environment::createCommonMarkEnvironment();
        $environment->addInlineParser($mentionParser);

        $converter = new CommonMarkConverter([], $environment);

        $this->assertEquals($expected, \rtrim($converter->convertToHtml($input)));
    }

    public function testMentionParserWithNonMatchingRegex(): void
    {
        $input = 'See #123 for more information.';
        $expected = '<p>See #123 for more information.</p>';

        $mentionParser = new MentionParser('#', '/^[a-z]+/', $this->createMock(MentionGeneratorInterface::class));

        $environment = Environment::createCommonMarkEnvironment();
        $environment->addInlineParser($mentionParser);

        $converter = new CommonMarkConverter([], $environment);

        $this->assertEquals($expected, \rtrim($converter->convertToHtml($input)));
    }

    public function testMentionParserWithNullUrl(): void
    {
        $input = 'See #123 for more information.';
        $expected = '<p>See #123 for more information.</p>';

        $returnsNull = $this->createMock(MentionGeneratorInterface::class);
        $returnsNull->method('generateMention')->willReturn(null);

        $mentionParser = new MentionParser('#', '/^\d+/', $returnsNull);

        $environment = Environment::createCommonMarkEnvironment();
        $environment->addInlineParser($mentionParser);

        $converter = new CommonMarkConverter([], $environment);

        $this->assertEquals($expected, \rtrim($converter->convertToHtml($input)));
    }

    public function testMentionParserUsingCallback(): void
    {
        $callable = function (Mention $mention) {
            return $mention
                // Stuff the three params into the URL just to prove we received them all properly
                ->setUrl(\sprintf('https://www.example.com/%s/%s/%s', $mention->getIdentifier(), $mention->getLabel(), $mention->getSymbol()))
                // Change the label
                ->setLabel('Replaced Label');
        };

        $input = 'This should parse #123.';
        $expected = '<p>This should parse <a href="https://www.example.com/123/#123/#">Replaced Label</a>.</p>';

        $mentionParser = MentionParser::createWithCallback('#', '/^\d+/', $callable);

        $environment = Environment::createCommonMarkEnvironment();
        $environment->addInlineParser($mentionParser);

        $converter = new CommonMarkConverter([], $environment);

        $this->assertEquals($expected, \rtrim($converter->convertToHtml($input)));
    }

    public function testMentionParserUsingCallbackReturnsAbstractInline(): void
    {
        $callable = function (Mention $mention) {
            // Pretend callback does some access logic to determine visibility.
            $emphasis = new Emphasis();
            $emphasis->appendChild(new Text('[members only]'));

            return $emphasis;
        };

        $input = 'This should parse #123.';
        $expected = '<p>This should parse <em>[members only]</em>.</p>';

        $mentionParser = MentionParser::createWithCallback('#', '/^\d+/', $callable);

        $environment = Environment::createCommonMarkEnvironment();
        $environment->addInlineParser($mentionParser);

        $converter = new CommonMarkConverter([], $environment);

        $this->assertEquals($expected, \rtrim($converter->convertToHtml($input)));
    }

    public function testMentionParserWithNonWordCharacterBefore(): void
    {
        $input = "Test\n#123 for more information.";
        $expected = "<p>Test\n<a href=\"https://www.example.com/123\">#123</a> for more information.</p>";

        $mentionParser = new MentionParser(
            '#',
            '/\d+/',
            new StringTemplateLinkGenerator('https://www.example.com/%s')
        );

        $environment = Environment::createCommonMarkEnvironment();
        $environment->addInlineParser($mentionParser);

        $converter = new CommonMarkConverter([], $environment);

        $this->assertEquals($expected, \rtrim((string) $converter->convertToHtml($input)));
    }
}
