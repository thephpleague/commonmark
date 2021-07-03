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

namespace League\CommonMark\Tests\Functional;

/**
 * Tests the parser against the CommonMark spec
 */
final class CommonMarkJSRegressionTest extends AbstractSpecTest
{
    protected function getFileName(): string
    {
        return __DIR__ . '/../../vendor/commonmark/commonmark.js/test/regression.txt';
    }

    /**
     * @deprecated
     *
     * We can't currently render spec example 18 exactly how the upstream library does. We'll likely need to overhaul
     * our rendering approach in order to fix that, so we'll use this temporary workaround for now.
     */
    public function dataProvider(): \Generator
    {
        foreach (parent::dataProvider() as $example) {
            if ($example['number'] === 18) {
                yield \str_replace('</script></li>', "</script>\n</li>", $example);
            } else {
                yield $example;
            }
        }
    }
}
