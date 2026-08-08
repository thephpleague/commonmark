<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace League\CommonMark\Tests\Functional\Extension\Attributes;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\Attributes\AttributesExtension;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\MarkdownConverter;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * The HTML5 tokenizer treats U+000C as whitespace between attributes, so a name carrying one
 * reaches the browser as the bare name - "\x0Conclick" fires as a real onclick handler. Payloads
 * are written with escape sequences because the byte is invisible in rendered source.
 *
 * @internal
 */
final class ObfuscatedAttributeNameTest extends TestCase
{
    /**
     * @dataProvider dataForTestObfuscatedAttributeNames
     */
    #[DataProvider('dataForTestObfuscatedAttributeNames')]
    public function testObfuscatedAttributeNamesAreFiltered(string $markdown, string $expected): void
    {
        $environment = new Environment(['html_input' => 'escape', 'allow_unsafe_links' => false]);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new AttributesExtension());

        $actual = \trim((string) (new MarkdownConverter($environment))->convert($markdown));

        $this->assertSame($expected, $actual);
    }

    /**
     * @return iterable<string, array<string>>
     */
    public static function dataForTestObfuscatedAttributeNames(): iterable
    {
        yield 'event handler' => [
            "hello {\x0Conclick=\"alert(1)\"}",
            '<p>hello</p>',
        ];

        yield 'event handler, block syntax' => [
            "# heading\n{\x0Conclick=\"alert(1)\"}",
            '<h1>heading</h1>',
        ];

        // Fires without any user interaction once the image fails to load
        yield 'event handler on image' => [
            "![x](https://example.com/x.png){\x0Conerror=\"alert(1)\"}",
            '<p><img src="https://example.com/x.png" alt="x" /></p>',
        ];

        // The injected href precedes the legitimate one, and HTML5 keeps the first of a
        // duplicate pair - so leaving it in place would hand the browser the javascript: URI
        yield 'unsafe link' => [
            "[click](https://example.com){\x0Chref=\"javascript:alert(1)\"}",
            '<p><a href="https://example.com">click</a></p>',
        ];

        // The byte is interior to the match here, so only the trim of the parsed name catches it
        yield 'unsafe link, trailing byte' => [
            "[click](https://example.com){href\x0C=\"javascript:alert(1)\"}",
            '<p><a href="https://example.com">click</a></p>',
        ];

        // Already covered by the default trim charlist; guards against a narrowed fix
        yield 'vertical tab is still stripped' => [
            "hello {\x0Bonclick=\"alert(1)\"}",
            '<p>hello</p>',
        ];
    }

    public function testWellFormedAttributeNamesStillWork(): void
    {
        $environment = new Environment(['html_input' => 'escape', 'allow_unsafe_links' => false]);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new AttributesExtension());

        $actual = \trim((string) (new MarkdownConverter($environment))->convert(
            'hello {#one .two data-x="3" aria-label="four" xml:lang="en"}'
        ));

        $this->assertSame(
            '<p class="two" id="one" data-x="3" aria-label="four" xml:lang="en">hello</p>',
            $actual
        );
    }
}
