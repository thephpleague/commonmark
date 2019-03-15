<?php

/*
 * This file is part of the league/commonmark-ext-autolink package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace league\CommonMark\Ext\Autolink\Test\Email;

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;
use League\CommonMark\Ext\Autolink\AutolinkExtension;
use PHPUnit\Framework\TestCase;

final class EmailAutolinkProcessorTest extends TestCase
{
    public function testEmailAutolinks()
    {
        $input = <<<EOT
This is some test content.

You can try emailing foo@example.com but that inbox doesn't actually exist.

> This processor can even handle email addresses like foo@example.com inside of blockquotes!

More fake emails:

 - foo@example.com
 - bar@example.com

However, @foo is not an email address and should be left as-is.
EOT;

        $expected = <<<EOT
<p>This is some test content.</p>
<p>You can try emailing <a href="mailto:foo@example.com">foo@example.com</a> but that inbox doesn't actually exist.</p>
<blockquote>
<p>This processor can even handle email addresses like <a href="mailto:foo@example.com">foo@example.com</a> inside of blockquotes!</p>
</blockquote>
<p>More fake emails:</p>
<ul>
<li>
<a href="mailto:foo@example.com">foo@example.com</a>
</li>
<li>
<a href="mailto:bar@example.com">bar@example.com</a>
</li>
</ul>
<p>However, @foo is not an email address and should be left as-is.</p>

EOT;

        $environment = Environment::createCommonMarkEnvironment();
        $environment->addExtension(new AutolinkExtension());

        $converter = new CommonMarkConverter([], $environment);

        $this->assertEquals($expected, $converter->convertToHtml($input));
    }
}
