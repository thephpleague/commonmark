<?php

/*
 * This file is part of the league/commonmark-ext-autolink package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Ext\Autolink\Test\Email;

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;
use League\CommonMark\Ext\Autolink\AutolinkExtension;
use PHPUnit\Framework\TestCase;

final class EmailAutolinkProcessorTest extends TestCase
{
    /**
     * @param string $input
     * @param string $expected
     *
     * @dataProvider dataProviderForEmailAutolinks
     */
    public function testEmailAutolinks($input, $expected)
    {
        $environment = Environment::createCommonMarkEnvironment();
        $environment->addExtension(new AutolinkExtension());

        $converter = new CommonMarkConverter([], $environment);

        $this->assertEquals($expected, \trim($converter->convertToHtml($input)));
    }

    public function dataProviderForEmailAutolinks()
    {
        yield ['You can try emailing foo@example.com but that inbox doesn\'t actually exist.', '<p>You can try emailing <a href="mailto:foo@example.com">foo@example.com</a> but that inbox doesn\'t actually exist.</p>'];
        yield ['> This processor can even handle email addresses like foo@example.com inside of blockquotes!', "<blockquote>\n<p>This processor can even handle email addresses like <a href=\"mailto:foo@example.com\">foo@example.com</a> inside of blockquotes!</p>\n</blockquote>"];
        yield ['@invalid', '<p>@invalid</p>'];
    }
}
