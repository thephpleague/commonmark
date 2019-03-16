<?php

/*
 * This file is part of the league/commonmark-ext-autolink package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Ext\Autolink\Test\Url;

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;
use League\CommonMark\Ext\Autolink\AutolinkExtension;
use PHPUnit\Framework\TestCase;

final class UrlAutolinkProcessorTest extends TestCase
{
    /**
     * @param string $input
     * @param string $expected
     *
     * @dataProvider dataProviderForAutolinkTests
     */
    public function testUrlAutolinks($input, $expected)
    {
        $environment = Environment::createCommonMarkEnvironment();
        $environment->addExtension(new AutolinkExtension());

        $converter = new CommonMarkConverter([], $environment);

        $this->assertEquals($expected, trim($converter->convertToHtml($input)));
    }

    public function dataProviderForAutolinkTests()
    {
        yield ['You can search on http://google.com for stuff.', '<p>You can search on <a href="http://google.com">http://google.com</a> for stuff.</p>'];

        // Tests of "incomplete" URLs
        yield ['google.com is missing www and/or a protocol', '<p>google.com is missing www and/or a protocol</p>'];
        yield ['http:/google.com is missing a slash', '<p>http:/google.com is missing a slash</p>'];
        yield ['javascript:alert(0); doesn\'t match the supported protocols', '<p>javascript:alert(0); doesn\'t match the supported protocols</p>'];

        // Tests involving trailing characters
        yield ['Maybe you\'re interested in https://www.google.com/search?q=php+commonmark!', '<p>Maybe you\'re interested in <a href="https://www.google.com/search?q=php+commonmark">https://www.google.com/search?q=php+commonmark</a>!</p>'];
        yield ['Or perhaps you\'re looking for my personal website https://www.colinodell.com...?', '<p>Or perhaps you\'re looking for my personal website <a href="https://www.colinodell.com">https://www.colinodell.com</a>...?</p>'];
    }
}
