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

namespace League\CommonMark\Tests\Unit\Normalizer;

use League\CommonMark\Normalizer\SlugNormalizer;
use League\Config\ConfigurationInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class SlugNormalizerTest extends TestCase
{
    /**
     * @dataProvider dataProviderForTestNormalize
     */
    #[DataProvider('dataProviderForTestNormalize')]
    public function testNormalize(string $input, string $expectedOutput): void
    {
        $this->assertSame($expectedOutput, (new SlugNormalizer())->normalize($input));
    }

    /**
     * @return iterable<string[]>
     */
    public static function dataProviderForTestNormalize(): iterable
    {
        yield ['', ''];
        yield ['hello world', 'hello-world'];
        yield ['hello-world', 'hello-world'];
        yield ['hello     world', 'hello-world'];
        yield ['Hello World!', 'hello-world'];

        yield ['456*(&^3484389462342#$#$#$#$', '4563484389462342'];
        yield ['me&you', 'meyou'];
        yield ['special char ὐ here', 'special-char-ὐ-here'];
        yield ['ПРИСТАНЯМ СТРЕМЯТСЯ', 'пристаням-стремятся'];
        yield ['пристаням стремятся', 'пристаням-стремятся'];
        yield ['emoji 😂 example', 'emoji--example'];
        yield ['One ½ half', 'one--half'];
        yield ['Roman ↁ example', 'roman-ↁ-example'];
        yield ['Here\'s a Ǆ digraph', 'heres-a-ǆ-digraph'];
        yield ['Here\'s another ǆ digraph', 'heres-another-ǆ-digraph'];
        yield ['Unicode x² superscript', 'unicode-x-superscript'];
        yield ['Equal = sign', 'equal--sign'];
        yield ['Tabs	in	here', 'tabs-in-here'];
        yield ['Tabs-	-in-	-here-too', 'tabs---in---here-too'];
        yield ['We-love---dashes even with -lots- of    spaces', 'we-love---dashes-even-with--lots--of-spaces'];
        yield ['LOUD NOISES', 'loud-noises'];
        yield ['ťęŝŧ', 'ťęŝŧ'];
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

    /**
     * @dataProvider dataProviderForTestNormalizeWithMaxLength
     */
    #[DataProvider('dataProviderForTestNormalizeWithMaxLength')]
    public function testNormalizeWithMaxLength(string $input, int $maxLength, string $expectedOutput): void
    {
        $this->assertSame($expectedOutput, (new SlugNormalizer())->normalize($input, ['length' => $maxLength]));
    }

    /**
     * @return iterable<mixed>
     */
    public static function dataProviderForTestNormalizeWithMaxLength(): iterable
    {
        yield ['Hello World', 8, 'hello-wo'];
        yield ['Hello World', 999, 'hello-world'];
        yield ['Hello World', 0, 'hello-world'];
    }

    public function testNormalizerWithDefaultMaxLength(): void
    {
        $config = $this->createMock(ConfigurationInterface::class);
        $config->expects($this->once())->method('get')->with('slug_normalizer/max_length')->willReturn(8);

        $normalizer = new SlugNormalizer();
        $normalizer->setConfiguration($config);

        $this->assertSame('hello-wo', $normalizer->normalize('Hello World'));
    }
}
