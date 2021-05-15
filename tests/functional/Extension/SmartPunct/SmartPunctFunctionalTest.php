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

namespace League\CommonMark\Tests\Functional\Extension\SmartPunct;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\SmartPunct\SmartPunctExtension;
use League\CommonMark\MarkdownConverter;
use League\CommonMark\Tests\Functional\AbstractSpecTest;

/**
 * Tests the parser against the CommonMark spec
 */
final class SmartPunctFunctionalTest extends AbstractSpecTest
{
    protected function setUp(): void
    {
        $environment = new Environment();
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new SmartPunctExtension());

        $this->converter = new MarkdownConverter($environment);
    }

    protected function getFileName(): string
    {
        return __DIR__ . '/../../../../vendor/commonmark/commonmark.js/test/smart_punct.txt';
    }
}
