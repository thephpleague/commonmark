<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Tests\Unit\Extension\HeadingPermalink\Slug;

use League\CommonMark\Extension\HeadingPermalink\Slug\DefaultSlugGenerator;
use PHPUnit\Framework\TestCase;

/**
 * @deprecated
 *
 * @group legacy
 */
final class DefaultSlugGeneratorTest extends TestCase
{
    /**
     * @dataProvider dataProviderForTestCreateSlug
     */
    public function testCreateSlug($input, $expectedOutput)
    {
        $generator = new DefaultSlugGenerator();
        $this->assertEquals($expectedOutput, $generator->createSlug($input));
    }

    public function dataProviderForTestCreateSlug()
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

        yield ['අත්හදා බලන මාතෘකාව',    'අත්හදා-බලන-මාතෘකාව'];
        yield ['අත්හදා බලන මාතෘකාව -',  'අත්හදා-බලන-මාතෘකාව--'];
        yield ['අත්හදා බලන මාතෘකාව - ', 'අත්හදා-බලන-මාතෘකාව--'];
        yield ['අත්හදා බලන මාතෘකාව - අ', 'අත්හදා-බලන-මාතෘකාව---අ'];

        yield ['测试标题',     '测试标题'];
        yield ['测试 # 标题',  '测试--标题'];
        yield ['测试 x² 标题', '测试-x-标题'];
        yield ['試験タイトル', '試験タイトル'];
    }
}
