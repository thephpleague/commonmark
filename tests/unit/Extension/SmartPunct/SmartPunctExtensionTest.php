<?php

declare(strict_types=1);

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

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\SmartPunct\SmartPunctExtension;
use League\CommonMark\MarkdownConverter;
use PHPUnit\Framework\TestCase;

/**
 * Tests the extension
 */
final class SmartPunctExtensionTest extends TestCase
{
    public function testDefaultConfiguration(): void
    {
        $environment = new Environment();
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new SmartPunctExtension());

        $converter    = new MarkdownConverter($environment);
        $actualResult = $converter->convert('"double" \'single\'');
        $this->assertEquals("<p>“double” ‘single’</p>\n", $actualResult);
    }

    public function testCustomConfiguration(): void
    {
        $environment = new Environment([
            'smartpunct' => [
                'double_quote_opener' => '«',
                'double_quote_closer' => '»',
                'single_quote_opener' => '‹',
                'single_quote_closer' => '›',
            ],
        ]);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new SmartPunctExtension());

        $converter = new MarkdownConverter($environment);

        $actualResult = $converter->convert('"double" \'single\'');
        $this->assertEquals("<p>«double» ‹single›</p>\n", $actualResult);
    }
}
