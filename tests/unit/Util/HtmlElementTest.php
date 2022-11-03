<?php

declare(strict_types=1);

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (https://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Tests\Unit\Util;

use League\CommonMark\Util\HtmlElement;
use PHPUnit\Framework\TestCase;

final class HtmlElementTest extends TestCase
{
    public function testConstructorOneArgument(): void
    {
        $p = new HtmlElement('p');
        $this->assertEquals('p', $p->getTagName());
        $this->assertEmpty($p->getAllAttributes());
        $this->assertEmpty($p->getContents());
    }

    public function testConstructorTwoArguments(): void
    {
        $img = new HtmlElement('img', ['src' => 'foo.jpg']);
        $this->assertEquals('img', $img->getTagName());
        $this->assertCount(1, $img->getAllAttributes());
        $this->assertEquals('foo.jpg', $img->getAttribute('src'));
        $this->assertEmpty($img->getContents());
    }

    public function testConstructorThreeArguments(): void
    {
        $li = new HtmlElement('li', ['class' => 'odd'], 'Foo');
        $this->assertEquals('li', $li->getTagName());
        $this->assertCount(1, $li->getAllAttributes());
        $this->assertEquals('odd', $li->getAttribute('class'));
        $this->assertEquals('Foo', $li->getContents());
    }

    public function testNonSelfClosingElement(): void
    {
        $p = new HtmlElement('p', [], '', false);

        $this->assertEquals('<p></p>', (string) $p);
    }

    public function testSelfClosingElement(): void
    {
        $hr = new HtmlElement('hr', [], '', true);

        $this->assertEquals('<hr />', (string) $hr);
    }

    public function testGetSetExistingAttribute(): void
    {
        $p = new HtmlElement('p', ['class' => 'foo']);
        $this->assertCount(1, $p->getAllAttributes());
        $this->assertEquals('foo', $p->getAttribute('class'));

        $p->setAttribute('class', 'bar');
        $this->assertCount(1, $p->getAllAttributes());
        $this->assertEquals('bar', $p->getAttribute('class'));
    }

    public function testGetSetNonExistingAttribute(): void
    {
        $p = new HtmlElement('p', ['class' => 'foo']);
        $this->assertCount(1, $p->getAllAttributes());
        $this->assertNull($p->getAttribute('id'));

        $p->setAttribute('id', 'bar');
        $this->assertCount(2, $p->getAllAttributes());
        $this->assertEquals('bar', $p->getAttribute('id'));
        $this->assertEquals('foo', $p->getAttribute('class'));
    }

    public function testGetSetAttributeWithStringAndArrayValues(): void
    {
        $p = new HtmlElement('p', ['class' => ['foo', 'bar']]);
        $this->assertCount(1, $p->getAllAttributes());
        $this->assertSame('foo bar', $p->getAttribute('class'));

        $p->setAttribute('class', 'baz');
        $this->assertSame('baz', $p->getAttribute('class'));

        $p->setAttribute('class', ['foo', 'bar', 'baz']);
        $this->assertSame('foo bar baz', $p->getAttribute('class'));

        $p->setAttribute('class', 'foo bar');
        $this->assertSame('foo bar', $p->getAttribute('class'));
    }

    public function testAttributesWithArrayValues(): void
    {
        $p = new HtmlElement('p', ['class' => ['a', 'b', 'a']]);
        $this->assertCount(1, $p->getAllAttributes());
        $this->assertSame('a b', $p->getAttribute('class'));
        $this->assertSame('<p class="a b"></p>', $p->__toString());

        $p->setAttribute('class', ['foo', 'bar', 'foo']);
        $this->assertSame('foo bar', $p->getAttribute('class'));
        $this->assertSame('<p class="foo bar"></p>', $p->__toString());

        // String attribute values do not have duplicate values removed
        $p->setAttribute('class', 'x y z x a');
        $this->assertSame('x y z x a', $p->getAttribute('class'));
        $this->assertSame('<p class="x y z x a"></p>', $p->__toString());
    }

    public function testAttributesWithBooleanTrueValues(): void
    {
        $checkbox = new HtmlElement('input', ['type' => 'checkbox', 'checked' => true], '', true);
        $this->assertSame('<input type="checkbox" checked>', $checkbox->__toString());

        $checkbox->setAttribute('checked', false);
        $this->assertSame('<input type="checkbox">', $checkbox->__toString());

        $checkbox->setAttribute('checked', true);
        $this->assertSame('<input type="checkbox" checked>', $checkbox->__toString());

        $checkbox->setAttribute('disabled'); // implicitly true
        $this->assertSame('<input type="checkbox" checked disabled>', $checkbox->__toString());
    }

    public function testToString(): void
    {
        $img = new HtmlElement('img', [], '', true);
        $p   = new HtmlElement('p');
        $div = new HtmlElement('div');
        $div->setContents([$p, $img]);

        $this->assertIsString($div->getContents(true));
        $this->assertEquals('<p></p><img />', $div->getContents(true));

        $this->assertEquals('<div><p></p><img /></div>', $div->__toString());
    }

    public function testToStringWithUnescapedAttribute(): void
    {
        $element = new HtmlElement('p', ['id' => 'foo', 'class' => 'test" onclick="javascript:doBadThings();'], 'click me');

        $this->assertEquals('<p id="foo" class="test&quot; onclick=&quot;javascript:doBadThings();">click me</p>', $element->__toString());
    }

    public function testNullContentConstructor(): void
    {
        $img = new HtmlElement('img', [], null);
        $this->assertTrue($img->getContents(false) === '');
    }

    public function testNullContentSetter(): void
    {
        $img = new HtmlElement('img');
        $img->setContents(null);
        $this->assertTrue($img->getContents(false) === '');
    }

    /**
     * See https://github.com/thephpleague/commonmark/issues/376
     */
    public function testRegressionWith0NotBeingRendered(): void
    {
        $element = new HtmlElement('em');
        $element->setContents('0');
        $this->assertSame('0', $element->getContents());

        $element = new HtmlElement('em', [], '0');
        $this->assertSame('0', $element->getContents());
    }
}
