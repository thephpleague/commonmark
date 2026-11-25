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

namespace League\CommonMark\Tests\Functional\Extension\SmartPunct;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\Mention\MentionParser;
use League\CommonMark\Extension\SmartPunct\SmartPunctExtension;
use League\CommonMark\MarkdownConverter;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Other inline parsers must get the same chances to parse inside runs of periods and hyphens as before
 */
final class SmartPunctWithOtherParsersTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    #[DataProvider('dataProvider')]
    public function testHigherPriorityParserInsideRun(string $input, string $expected): void
    {
        $environment = new Environment();
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new SmartPunctExtension());
        $environment->addInlineParser(MentionParser::createWithStringTemplate('dots', '...', '[a-z]+', '/u/%s'), 100);
        $environment->addInlineParser(MentionParser::createWithStringTemplate('dashes', '--', '[a-z]+', '/d/%s'), 100);
        // Consumes a backslash, so a period after it isn't escaped
        $environment->addInlineParser(MentionParser::createWithStringTemplate('backslash', '@', '[a-z]+\\\\', '/b/%s'), 100);

        $this->assertSame($expected, (string) (new MarkdownConverter($environment))->convert($input));
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function dataProvider(): iterable
    {
        yield 'mention after an ellipsis' => ['......alice', "<p>…<a href=\"/u/alice\">...alice</a></p>\n"];
        yield 'mention after two ellipses' => ['.........alice', "<p>……<a href=\"/u/alice\">...alice</a></p>\n"];
        yield 'mention after an escaped period' => ['\\....alice', "<p>.<a href=\"/u/alice\">...alice</a></p>\n"];
        yield 'mention starting a run of hyphens' => ['a --bob', "<p>a <a href=\"/d/bob\">--bob</a></p>\n"];
        yield 'mention inside a run of hyphens' => ['a ----bob', "<p>a ––bob</p>\n"];
        yield 'run of periods after a backslash consumed by another parser' => ['@a\\......', "<p><a href=\"/b/a\\\">@a\\</a>……</p>\n"];
        yield 'spaced periods after a backslash consumed by another parser' => ['@a\\. . . . . .', "<p><a href=\"/b/a\\\">@a\\</a>… …</p>\n"];
        yield 'ellipsis then spaced ellipsis after a consumed backslash' => ['@a\\... . . .', "<p><a href=\"/b/a\\\">@a\\</a>… …</p>\n"];
        yield 'spaced ellipsis then ellipsis after a consumed backslash' => ['@a\\. . ....', "<p><a href=\"/b/a\\\">@a\\</a>……</p>\n"];
        yield 'two ellipses then spaced periods after a consumed backslash' => ['@a\\.... . . .', "<p><a href=\"/b/a\\\">@a\\</a>…… .</p>\n"];
        yield 'spaced ellipsis then more periods after a consumed backslash' => ['@a\\. . . ....', "<p><a href=\"/b/a\\\">@a\\</a>… ….</p>\n"];
        yield 'text between ellipses after a consumed backslash' => ['@a\\... .. . . x', "<p><a href=\"/b/a\\\">@a\\</a>… .… x</p>\n"];
    }
}
