<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (http://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Tests\Unit\Extension\SmartPunct;

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;
use League\CommonMark\Extension\SmartPunct\SmartPunctExtension;
use PHPUnit\Framework\TestCase;

/**
 * Tests the extension
 */
final class SmartPunctExtensionTest extends TestCase
{
    /**
     * @var Environment
     */
    protected $environment;

    protected function setUp(): void
    {
        $this->environment = Environment::createCommonMarkEnvironment();
        $this->environment->addExtension(new SmartPunctExtension());
    }

    public function testDefaultConfiguration()
    {
        $converter = new CommonMarkConverter([], $this->environment);
        $actualResult = $converter->convertToHtml('"double" \'single\'');
        $this->assertEquals("<p>“double” ‘single’</p>\n", $actualResult);
    }

    public function testCustomConfiguration()
    {
        $converter = new CommonMarkConverter([
            'smartpunct' => [
                'double_quote_opener' => '«',
                'double_quote_closer' => '»',
                'single_quote_opener' => '‹',
                'single_quote_closer' => '›',
            ],
        ], $this->environment);

        $actualResult = $converter->convertToHtml('"double" \'single\'');
        $this->assertEquals("<p>«double» ‹single›</p>\n", $actualResult);
    }
}
