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

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\Mention\Generator\MentionGeneratorInterface;
use League\CommonMark\Extension\Mention\Mention;
use League\CommonMark\Extension\Mention\MentionExtension;
use League\CommonMark\MarkdownConverter;
use League\CommonMark\Node\Inline\AbstractInline;
use League\CommonMark\Parser\MarkdownParser;
use League\CommonMark\Xml\XmlRenderer;
use League\Config\Exception\InvalidConfigurationException;
use PHPUnit\Framework\TestCase;

final class MentionExtensionTest extends TestCase
{
    public function testNoConfig(): void
    {
        $input = <<<'EOT'
You can follow the author of this library on GitHub - he's @colinodell!
EOT;

        $expected = <<<'EOT'
<p>You can follow the author of this library on GitHub - he's @colinodell!</p>

EOT;

        $environment = new Environment();
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new MentionExtension());

        $converter = new MarkdownConverter($environment);

        $this->assertEquals($expected, $converter->convert($input)->getContent());
    }

    public function testConfigStringGenerator(): void
    {
        $input = <<<'EOT'
You can follow the author of this library on GitHub - he's @colinodell!
EOT;

        $expected = <<<'EOT'
<p>You can follow the author of this library on GitHub - he's <a href="https://github.com/colinodell">@colinodell</a>!</p>

EOT;

        $environment = new Environment([
            'mentions' => [
                'github_handle' => [
                    'prefix'    => '@',
                    'pattern'   => '[a-z\d](?:[a-z\d]|-(?=[a-z\d])){0,38}(?!\w)',
                    'generator' => 'https://github.com/%s',
                ],
            ],
        ]);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new MentionExtension());

        $converter = new MarkdownConverter($environment);

        $this->assertEquals($expected, $converter->convert($input)->getContent());
    }

    public function testConfigCallableGenerator(): void
    {
        $input = <<<'EOT'
You can follow the author of this library on GitHub - he's @colinodell!
EOT;

        $expected = <<<'EOT'
<p>You can follow the author of this library on GitHub - he's <a href="https://github.com/colinodell">@colinodell</a>!</p>

EOT;

        $environment = new Environment([
            'mentions' => [
                'github_handle' => [
                    'prefix'    => '@',
                    'pattern'   => '[a-z\d](?:[a-z\d]|-(?=[a-z\d])){0,38}(?!\w)',
                    'generator' => static function (Mention $mention) {
                        $mention->setUrl(\sprintf('https://github.com/%s', $mention->getIdentifier()));

                        return $mention;
                    },
                ],
            ],
        ]);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new MentionExtension());

        $converter = new MarkdownConverter($environment);

        $this->assertEquals($expected, $converter->convert($input)->getContent());
    }

    public function testConfigObjectImplementingMentionGeneratorInterface(): void
    {
        $input = <<<'EOT'
You can follow the author of this library on GitHub - he's @colinodell!
EOT;

        $expected = <<<'EOT'
<p>You can follow the author of this library on GitHub - he's <a href="https://github.com/colinodell">@colinodell</a>!</p>

EOT;

        $environment = new Environment([
            'mentions' => [
                'github_handle' => [
                    'prefix'    => '@',
                    'pattern'   => '[a-z\d](?:[a-z\d]|-(?=[a-z\d])){0,38}(?!\w)',
                    'generator' => new class () implements MentionGeneratorInterface {
                        public function generateMention(Mention $mention): ?AbstractInline
                        {
                            $mention->setUrl(\sprintf('https://github.com/%s', $mention->getIdentifier()));

                            return $mention;
                        }
                    },
                ],
            ],
        ]);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new MentionExtension());

        $converter = new MarkdownConverter($environment);

        $this->assertEquals($expected, $converter->convert($input)->getContent());
    }

    public function testConfigUnknownGenerator(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $environment = new Environment([
            'mentions' => [
                'github_handle' => [
                    'prefix'    => '@',
                    'pattern'   => '[a-z\d](?:[a-z\d]|-(?=[a-z\d])){0,38}(?!\w)',
                    'generator' => new \stdClass(),
                ],
            ],
        ]);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new MentionExtension());

        $converter = new MarkdownConverter($environment);

        $converter->convert('');
    }

    public function testLegacySymbolOption(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $environment = new Environment([
            'mentions' => [
                'github_handle' => [
                    'symbol'    => '@',
                    'pattern'   => '[a-z\d](?:[a-z\d]|-(?=[a-z\d])){0,38}(?!\w)',
                    'generator' => 'https://github.com/%s',
                ],
            ],
        ]);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new MentionExtension());

        $converter = new MarkdownConverter($environment);

        $converter->convert('foo');
    }

    public function testWithFullRegexOption(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $environment = new Environment([
            'mentions' => [
                'github_handle' => [
                    'prefix'    => '@',
                    'pattern'   => '/[a-z\d](?:[a-z\d]|-(?=[a-z\d])){0,38}(?!\w)/i',
                    'generator' => 'https://github.com/%s',
                ],
            ],
        ]);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new MentionExtension());

        $converter = new MarkdownConverter($environment);

        $converter->convert('foo');
    }

    public function testXmlRendering(): void
    {
        $input = <<<'EOT'
You can follow the author of this library on GitHub - he's @colinodell!
EOT;

        $expected = <<<'EOT'
<?xml version="1.0" encoding="UTF-8"?>
<document xmlns="http://commonmark.org/xml/1.0">
    <paragraph>
        <text>You can follow the author of this library on GitHub - he's </text>
        <link destination="https://github.com/colinodell" title="">
            <text>@colinodell</text>
        </link>
        <text>!</text>
    </paragraph>
</document>

EOT;

        $environment = new Environment([
            'mentions' => [
                'github_handle' => [
                    'prefix'    => '@',
                    'pattern'   => '[a-z\d](?:[a-z\d]|-(?=[a-z\d])){0,38}(?!\w)',
                    'generator' => new class () implements MentionGeneratorInterface {
                        public function generateMention(Mention $mention): ?AbstractInline
                        {
                            $mention->setUrl(\sprintf('https://github.com/%s', $mention->getIdentifier()));

                            return $mention;
                        }
                    },
                ],
            ],
        ]);

        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new MentionExtension());

        $document = (new MarkdownParser($environment))->parse($input);
        $xml      = (new XmlRenderer($environment))->renderDocument($document)->getContent();

        $this->assertSame($expected, $xml);
    }

    public function testMentionLikeLabelInExistingLinks(): void
    {
        $input = <<<'EOT'
Try [asking **@driesvints**](https://github.com/driesvints).
EOT;

        $expected = <<<'EOT'
<p>Try <a href="https://github.com/driesvints">asking <strong>@driesvints</strong></a>.</p>

EOT;

        $environment = new Environment([
            'mentions' => [
                'username' => [
                    'prefix'    => '@',
                    'pattern'   => '[a-z\d](?:[a-z\d]|-(?=[a-z\d])){0,38}(?!\w)',
                    'generator' => 'https://github.com/user/%s',
                ],
            ],
        ]);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new MentionExtension());

        $converter = new MarkdownConverter($environment);

        $this->assertSame($expected, $converter->convert($input)->getContent());
    }
}
