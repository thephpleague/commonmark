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

namespace League\CommonMark\Tests\Functional\Extension\DisallowedRawHtml;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\DisallowedRawHtml\DisallowedRawHtmlExtension;
use League\CommonMark\MarkdownConverter;
use PHPUnit\Framework\TestCase;

final class DisallowedRawHtmlExtensionTest extends TestCase
{
    public function testDisallowedRawHtmlExtensionWithSpecExample(): void
    {
        $input = <<<'MD'
<strong> <title> <style> <em>

<blockquote>
  <xmp> is disallowed.  <XMP> is also disallowed.
</blockquote>
MD;

        $expected = <<<'HTML'
<p><strong> &lt;title> &lt;style> <em></p>
<blockquote>
  &lt;xmp> is disallowed.  &lt;XMP> is also disallowed.
</blockquote>

HTML;

        $environment = new Environment();
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new DisallowedRawHtmlExtension());
        $converter = new MarkdownConverter($environment);

        $this->assertSame($expected, (string) $converter->convert($input));
    }

    public function testIndividualHtmlTagsAsBlocks(): void
    {
        $input = <<<'MD'
<title>My Cool Website</title>
<textarea>
  foo=bar
</textarea>

<style>* { display: none; </style>

<xmp>Itallic font should be marked up using the <i> and </i> tags.</xmp>

<iframe width="560" height="315" src="https://www.youtube.com/embed/dQw4w9WgXcQ" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

<noembed><h1>Alternative content</h1></noembed>

<noframes><h1>Alternative content</h1></noframes>

<hr>
<script type="application/javascript">alert('XSS is fun!')</script>
<plaintext>foo</plaintext>
MD;

        $expected = <<<'HTML'
&lt;title>My Cool Website&lt;/title>
&lt;textarea>
  foo=bar
&lt;/textarea>
&lt;style>* { display: none; &lt;/style>
<p>&lt;xmp>Itallic font should be marked up using the <i> and </i> tags.&lt;/xmp></p>
&lt;iframe width="560" height="315" src="https://www.youtube.com/embed/dQw4w9WgXcQ" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen>&lt;/iframe>
<p>&lt;noembed><h1>Alternative content</h1>&lt;/noembed></p>
&lt;noframes><h1>Alternative content</h1>&lt;/noframes>
<hr>
&lt;script type="application/javascript">alert('XSS is fun!')&lt;/script>
&lt;plaintext>foo&lt;/plaintext>

HTML;

        $environment = new Environment();
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new DisallowedRawHtmlExtension());
        $converter = new MarkdownConverter($environment);

        $this->assertSame($expected, (string) $converter->convert($input));
    }
}
