<?php

declare(strict_types=1);

namespace League\CommonMark\Tests\Unit\Normalizer;

use League\CommonMark\Normalizer\UniqueSlugNormalizer;
use PHPUnit\Framework\TestCase;

final class UniqueSlugNormalizerTest extends TestCase
{
    public function testNormalize(): void
    {
        $normalizer = new UniqueSlugNormalizer();

        // Providing the same input multiple times should give different slugs
        $this->assertSame('test', $normalizer->normalize('Test'));
        $this->assertSame('test-1', $normalizer->normalize('Test'));
        $this->assertSame('test-2', $normalizer->normalize('Test'));

        // This behavior should also work for different inputs that normally produce the same slug
        $this->assertSame('hello-world', $normalizer->normalize('hello world'));
        $this->assertSame('hello-world-1', $normalizer->normalize('hello-world'));
        $this->assertSame('hello-world-2', $normalizer->normalize('Hello, World!'));
    }
}
