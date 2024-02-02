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

namespace League\CommonMark\Tests\Functional\Extension;

use League\CommonMark\GithubFlavoredMarkdownConverter;
use League\CommonMark\Tests\Functional\AbstractSpecTestCase;
use League\CommonMark\Util\SpecReader;

final class GithubFlavoredMarkdownExtensionTest extends AbstractSpecTestCase
{
    protected function setUp(): void
    {
        $this->converter = new GithubFlavoredMarkdownConverter();
    }

    public static function dataProvider(): \Generator
    {
        $tests = SpecReader::readFile(__DIR__ . '/../../../vendor/github/gfm/test/spec.txt');

        foreach ($tests as $title => $data) {
            // In the GFM spec, standard CommonMark tests are tagged 'example'
            // and we don't want to test those (because we test those against the
            // official CommonMark spec), but we DO want to test the GFM-specific ones
            // which will be tagged something like 'example autolink'
            if ($data['type'] !== 'example') {
                yield $title => $data;
            }
        }
    }
}
