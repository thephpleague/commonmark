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

namespace League\CommonMark\Tests\Unit\Extension\HeadingPermalink\SlugGenerator;

use League\CommonMark\Extension\CommonMark\Node\Inline\HtmlInline;
use League\CommonMark\Extension\CommonMark\Node\Inline\Strong;
use League\CommonMark\Extension\HeadingPermalink\SlugGenerator\DefaultSlugGenerator;
use League\CommonMark\Node\Block\Document;
use League\CommonMark\Node\Block\Paragraph;
use League\CommonMark\Node\Inline\Text;
use PHPUnit\Framework\TestCase;

final class DefaultSlugGeneratorTest extends TestCase
{
    public function testGenerateSlug(): void
    {
        $document = new Document();
        $document->appendChild($paragraph = new Paragraph());
        $paragraph->appendChild($strong = new Strong());
        $strong->appendChild(new Text('Hello'));
        $paragraph->appendChild(new Text(' World!'));

        $slugGenerator = new DefaultSlugGenerator();

        $this->assertSame('hello-world', $slugGenerator->generateSlug($document));
        $this->assertSame('hello-world', $slugGenerator->generateSlug($paragraph));
    }

    public function testGenerateSlugWithNoInnerTextContents(): void
    {
        $paragraph = new Paragraph();

        $slugGenerator = new DefaultSlugGenerator();

        $this->assertSame('', $slugGenerator->generateSlug($paragraph));
    }

    public function testGenerateSlugWithHtmlInContents(): void
    {
        $document = new Document();
        $document->appendChild($paragraph = new Paragraph());
        $paragraph->appendChild(new Text('I'));
        $paragraph->appendChild(new Text(' '));
        $paragraph->appendChild($html = new HtmlInline());
        $html->setLiteral('<strong>love</strong>');
        $paragraph->appendChild(new Text(' '));
        $paragraph->appendChild(new Text('CommonMark!'));

        $slugGenerator = new DefaultSlugGenerator();

        $this->assertSame('i-commonmark', $slugGenerator->generateSlug($document));
        $this->assertSame('i-commonmark', $slugGenerator->generateSlug($paragraph));
        $this->assertSame('', $slugGenerator->generateSlug($html));
    }

    /**
     * @dataProvider dataProviderForTestSlugifyText
     */
    public function testSlugifyText(string $input, string $expectedOutput): void
    {
        $this->assertEquals($expectedOutput, DefaultSlugGenerator::slugifyText($input));
    }

    /**
     * @return iterable<array<string, string>>
     */
    public function dataProviderForTestSlugifyText(): iterable
    {
        yield ['', ''];
        yield ['hello world', 'hello-world'];
        yield ['hello     world', 'hello-world'];
        yield ['Hello World!', 'hello-world'];

        yield ['456*(&^3484389462342#$#$#$#$', '4563484389462342'];
        yield ['me&you', 'meyou'];
        yield ['special char ὐ here', 'special-char-ὐ-here'];
        yield ['пристаням стремятся', 'пристаням-стремятся'];
        yield ['emoji 😂 example', 'emoji--example'];
        yield ['One ½ half', 'one--half'];
        yield ['Roman ↁ example', 'roman-ↁ-example'];
        yield ['Here\'s a Ǆ digraph', 'heres-a-ǆ-digraph'];
        yield ['Unicode x² superscript', 'unicode-x-superscript'];
        yield ['Equal = sign', 'equal--sign'];
        yield ['Tabs	in	here', 'tabs-in-here'];
        yield ['Tabs-	-in-	-here-too', 'tabs---in---here-too'];
        yield ['We-love---dashes even with -lots- of    spaces', 'we-love---dashes-even-with--lots--of-spaces'];
        yield ['LOUD NOISES', 'loud-noises'];
        yield ['ŤĘŜŦ', 'ťęŝŧ'];

        yield ["\nWho\nput\n\n newlines  \nin here?!\n", 'who-put-newlines-in-here'];

        yield ['අත්හදා බලන මාතෘකාව', 'අත්හදා-බලන-මාතෘකාව'];
        yield ['අත්හදා බලන මාතෘකාව -', 'අත්හදා-බලන-මාතෘකාව--'];
        yield ['අත්හදා බලන මාතෘකාව - ', 'අත්හදා-බලන-මාතෘකාව--'];
        yield ['අත්හදා බලන මාතෘකාව - අ', 'අත්හදා-බලන-මාතෘකාව---අ'];

        yield ['测试标题', '测试标题'];
        yield ['测试 # 标题', '测试--标题'];
        yield ['测试 x² 标题', '测试-x-标题'];
        yield ['試験タイトル', '試験タイトル'];
    }
}
