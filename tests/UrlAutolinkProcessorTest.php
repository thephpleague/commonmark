<?php

/*
 * This file is part of the league/commonmark-ext-autolink package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace league\CommonMark\Ext\Autolink\Test\Url;

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;
use League\CommonMark\Ext\Autolink\AutolinkExtension;
use PHPUnit\Framework\TestCase;

final class UrlAutolinkProcessorTest extends TestCase
{
    public function testEmUrlAutolinks()
    {
        $input = <<<EOT
You can search on http://google.com for stuff. For example, maybe you're interested in https://www.google.com/search?q=php+commonmark! Or perhaps you're looking for my personal website https://www.colinodell.com...?

All of those links above should be auto-converted. However, invalid or incomplete URLs like google.com and http:/google.com won't be converted. Also, javascript:alert(0); won't be converted because we never whitelisted that protocol.
EOT;

        $expected = <<<EOT
<p>You can search on <a href="http://google.com">http://google.com</a> for stuff. For example, maybe you're interested in <a href="https://www.google.com/search?q=php+commonmark">https://www.google.com/search?q=php+commonmark</a>! Or perhaps you're looking for my personal website <a href="https://www.colinodell.com">https://www.colinodell.com</a>...?</p>
<p>All of those links above should be auto-converted. However, invalid or incomplete URLs like google.com and http:/google.com won't be converted. Also, javascript:alert(0); won't be converted because we never whitelisted that protocol.</p>

EOT;

        $environment = Environment::createCommonMarkEnvironment();
        $environment->addExtension(new AutolinkExtension());

        $converter = new CommonMarkConverter([], $environment);

        $this->assertEquals($expected, $converter->convertToHtml($input));
    }
}
