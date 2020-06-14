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
use League\CommonMark\Extension\Mention\Mention;
use League\CommonMark\Extension\Mention\MentionExtension;
use PHPUnit\Framework\TestCase;

class MentionExtensionTest extends TestCase
{
    public function testNoConfig(): void
    {
        $input = <<<'EOT'
You can follow the author of this library on Github - he's @colinodell!
EOT;

        $expected = <<<'EOT'
<p>You can follow the author of this library on Github - he's @colinodell!</p>

EOT;

        $environment = Environment::createCommonMarkEnvironment();
        $environment->addExtension(new MentionExtension());

        $converter = new CommonMarkConverter([], $environment);

        $this->assertEquals($expected, $converter->convertToHtml($input));
    }

    public function testConfigStringGenerator(): void
    {
        $input = <<<'EOT'
You can follow the author of this library on Github - he's @colinodell!
EOT;

        $expected = <<<'EOT'
<p>You can follow the author of this library on Github - he's <a href="https://github.com/colinodell">@colinodell</a>!</p>

EOT;

        $environment = Environment::createCommonMarkEnvironment();
        $environment->addExtension(new MentionExtension());
        $environment->setConfig([
            'mentions' => [
                'github_handle' => [
                    'symbol'    => '@',
                    'regex'     => '/^[a-z\d](?:[a-z\d]|-(?=[a-z\d])){0,38}(?!\w)/',
                    'generator' => 'https://github.com/%s',
                ],
            ],
        ]);

        $converter = new CommonMarkConverter([], $environment);

        $this->assertEquals($expected, $converter->convertToHtml($input));
    }

    public function testConfigCallableGenerator(): void
    {
        $input = <<<'EOT'
You can follow the author of this library on Github - he's @colinodell!
EOT;

        $expected = <<<'EOT'
<p>You can follow the author of this library on Github - he's <a href="https://github.com/colinodell">@colinodell</a>!</p>

EOT;

        $environment = Environment::createCommonMarkEnvironment();
        $environment->addExtension(new MentionExtension());
        $environment->setConfig([
            'mentions' => [
                'github_handle' => [
                    'symbol'    => '@',
                    'regex'     => '/^[a-z\d](?:[a-z\d]|-(?=[a-z\d])){0,38}(?!\w)/',
                    'generator' => function (Mention $mention) {
                        $mention->setUrl(\sprintf('https://github.com/%s', $mention->getHandle()));
                    },
                ],
            ],
        ]);

        $converter = new CommonMarkConverter([], $environment);

        $this->assertEquals($expected, $converter->convertToHtml($input));
    }

    public function testConfigUnknownGenerator(): void
    {
        $this->expectException(\RuntimeException::class);

        $environment = Environment::createCommonMarkEnvironment();
        $environment->addExtension(new MentionExtension());
        $environment->setConfig([
            'mentions' => [
                'github_handle' => [
                    'symbol'    => '@',
                    'regex'     => '/^[a-z\d](?:[a-z\d]|-(?=[a-z\d])){0,38}(?!\w)/',
                    'generator' => new \stdClass(),
                ],
            ],
        ]);

        $converter = new CommonMarkConverter([], $environment);

        $converter->convertToHtml('');
    }

    public function testCreateGithubHandleExtension(): void
    {
        $input = <<<'EOT'
You can follow the author of this library on Github - he's @colinodell!
EOT;

        $expected = <<<'EOT'
<p>You can follow the author of this library on Github - he's <a href="https://github.com/colinodell">@colinodell</a>!</p>

EOT;

        $environment = Environment::createCommonMarkEnvironment();
        $environment->addExtension(new MentionExtension());
        MentionExtension::registerGitHubHandle($environment);

        $converter = new CommonMarkConverter([], $environment);

        $this->assertEquals($expected, $converter->convertToHtml($input));
    }

    public function testCreateGithubIssueExtension(): void
    {
        $input = <<<'EOT'
This feature was implemented thanks to #473 by Mark Carver.
EOT;

        $expected = <<<'EOT'
<p>This feature was implemented thanks to <a href="https://github.com/thephpleague/commonmark/issues/473">#473</a> by Mark Carver.</p>

EOT;

        $environment = Environment::createCommonMarkEnvironment();
        $environment->addExtension(new MentionExtension());
        MentionExtension::registerGitHubIssue($environment, 'thephpleague/commonmark');

        $converter = new CommonMarkConverter([], $environment);

        $this->assertEquals($expected, $converter->convertToHtml($input));
    }

    public function testCreateTwitterHandleExtension(): void
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
        $environment->addExtension(new MentionExtension());
        MentionExtension::registerTwitterHandle($environment);

        $converter = new CommonMarkConverter([], $environment);

        $this->assertEquals($expected, $converter->convertToHtml($input));
    }

    public function testMultipleSameSymbolException(): void
    {
        $this->expectException(\RuntimeException::class);

        $environment = Environment::createCommonMarkEnvironment();
        $environment->addExtension(new MentionExtension());
        MentionExtension::registerGitHubHandle($environment);
        MentionExtension::registerTwitterHandle($environment);

        $converter = new CommonMarkConverter([], $environment);

        $converter->convertToHtml('@colinodell');
    }
}
