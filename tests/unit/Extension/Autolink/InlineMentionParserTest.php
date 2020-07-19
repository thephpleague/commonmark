<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Tests\Unit\Extension\Autolink;

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;
use League\CommonMark\Extension\Autolink\InlineMentionParser;
use PHPUnit\Framework\TestCase;

/**
 * @deprecated
 *
 * @group legacy
 */
final class InlineMentionParserTest extends TestCase
{
    public function testTwitterMentionParser()
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
        $environment->addInlineParser(InlineMentionParser::createTwitterHandleParser());

        $converter = new CommonMarkConverter([], $environment);

        $this->assertEquals($expected, $converter->convertToHtml($input));
    }

    public function testGithubMentionParser()
    {
        $input = <<<'EOT'
You can follow the author of this library on Github - he's @colinodell!
EOT;

        $expected = <<<'EOT'
<p>You can follow the author of this library on Github - he's <a href="https://www.github.com/colinodell">@colinodell</a>!</p>

EOT;

        $environment = Environment::createCommonMarkEnvironment();
        $environment->addInlineParser(InlineMentionParser::createGithubHandleParser());

        $converter = new CommonMarkConverter([], $environment);

        $this->assertEquals($expected, $converter->convertToHtml($input));
    }
}
