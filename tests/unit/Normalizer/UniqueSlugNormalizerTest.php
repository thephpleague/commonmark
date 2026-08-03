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

    public function testNormalizeWhenSuffixWasAlreadyTakenByAnotherSlug(): void
    {
        $normalizer = new UniqueSlugNormalizer(new SlugNormalizer());

        // A slug which happens to look like a suffixed one occupies that suffix, so the
        // next collision has to skip past it instead of handing out a duplicate.
        $this->assertSame('test-1', $normalizer->normalize('test 1'));
        $this->assertSame('test', $normalizer->normalize('test'));
        $this->assertSame('test-2', $normalizer->normalize('test'));
    }

    public function testNormalizeWhenSuffixIsTakenAfterSuffixingHasBegun(): void
    {
        $normalizer = new UniqueSlugNormalizer(new SlugNormalizer());

        $this->assertSame('test', $normalizer->normalize('test'));
        $this->assertSame('test-1', $normalizer->normalize('test'));
        $this->assertSame('test-2', $normalizer->normalize('test 2'));
        $this->assertSame('test-3', $normalizer->normalize('test'));
        $this->assertSame('test-1-1', $normalizer->normalize('test 1'));
        $this->assertSame('test-4', $normalizer->normalize('test'));
    }

    public function testNormalizeEmptySlugs(): void
    {
        $normalizer = new UniqueSlugNormalizer(new SlugNormalizer());

        $this->assertSame('', $normalizer->normalize(''));
        $this->assertSame('-1', $normalizer->normalize(''));
        $this->assertSame('-2', $normalizer->normalize(''));
    }

    public function testClearHistoryForgetsPreviouslyUsedSuffixes(): void
    {
        $normalizer = new UniqueSlugNormalizer(new SlugNormalizer());

        $this->assertSame('test', $normalizer->normalize('test'));
        $this->assertSame('test-1', $normalizer->normalize('test'));

        $normalizer->clearHistory();

        $this->assertSame('test-1', $normalizer->normalize('test 1'));
        $this->assertSame('test', $normalizer->normalize('test'));
        $this->assertSame('test-2', $normalizer->normalize('test'));
    }
}
