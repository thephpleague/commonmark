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

namespace League\CommonMark\Tests\Unit\Normalizer;

use League\CommonMark\Normalizer\SlugNormalizer;
use League\CommonMark\Normalizer\UniqueSlugNormalizer;
use PHPUnit\Framework\TestCase;

final class UniqueSlugNormalizerTest extends TestCase
{
    public function testNormalize(): void
    {
        $normalizer = new UniqueSlugNormalizer(new SlugNormalizer());

        $this->assertSame('test', $normalizer->normalize('test'));
        $this->assertSame('test-1', $normalizer->normalize('test'));
        $this->assertSame('test-2', $normalizer->normalize('test'));
        $this->assertSame('test-3', $normalizer->normalize('test'));

        $normalizer->clearHistory();

        $this->assertSame('test', $normalizer->normalize('test'));
        $this->assertSame('test-1', $normalizer->normalize('test'));
        $this->assertSame('test-2', $normalizer->normalize('test'));
        $this->assertSame('test-3', $normalizer->normalize('test'));
    }
}
