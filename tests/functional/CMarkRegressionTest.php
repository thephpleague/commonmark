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

use League\CommonMark\Util\SpecReader;

/**
 * Tests the parser against the CommonMark spec
 */
final class CMarkRegressionTest extends AbstractSpecTestCase
{
    public static function dataProvider(): \Generator
    {
        $tests = SpecReader::readFile(__DIR__ . '/../../vendor/commonmark/cmark/test/regression.txt');
        foreach ($tests as $example) {
            // The case-fold test from example 21 fails on PHP 8.0.* and below due to the behavior of mb_convert_case().
            // See https://3v4l.org/7TeXJ.
            if (\PHP_VERSION_ID < 81000 && $example['number'] === 21) {
                continue;
            }

            yield $example;
        }
    }
}
