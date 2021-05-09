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

use League\CommonMark\Event\DocumentRenderedEvent;
use League\CommonMark\Normalizer\SlugNormalizer;
use League\CommonMark\Normalizer\UniqueSlugNormalizer;
use League\CommonMark\Output\RenderedContentInterface;
use PHPUnit\Framework\TestCase;

final class UniqueSlugNormalizerTest extends TestCase
{
    public function testWithDocumentScope(): void
    {
        $normalizer = new UniqueSlugNormalizer(new SlugNormalizer(), UniqueSlugNormalizer::SCOPE_DOCUMENT);

        $this->assertSame('document-test', $normalizer->normalize('document-test'));
        $this->assertSame('document-test-1', $normalizer->normalize('document-test'));
        $this->assertSame('document-test-2', $normalizer->normalize('document-test'));
        $this->assertSame('document-test-3', $normalizer->normalize('document-test'));

        $normalizer->onDocumentRendered(new DocumentRenderedEvent($this->createMock(RenderedContentInterface::class)));

        $this->assertSame('document-test', $normalizer->normalize('document-test'));
        $this->assertSame('document-test-1', $normalizer->normalize('document-test'));
        $this->assertSame('document-test-2', $normalizer->normalize('document-test'));
        $this->assertSame('document-test-3', $normalizer->normalize('document-test'));
    }

    public function testWithEnvironmentScope(): void
    {
        $normalizer = new UniqueSlugNormalizer(new SlugNormalizer(), UniqueSlugNormalizer::SCOPE_ENVIRONMENT);

        $this->assertSame('environment-test', $normalizer->normalize('environment-test'));
        $this->assertSame('environment-test-1', $normalizer->normalize('environment-test'));
        $this->assertSame('environment-test-2', $normalizer->normalize('environment-test'));
        $this->assertSame('environment-test-3', $normalizer->normalize('environment-test'));

        $normalizer->onDocumentRendered(new DocumentRenderedEvent($this->createMock(RenderedContentInterface::class)));

        $this->assertSame('environment-test-4', $normalizer->normalize('environment-test'));
        $this->assertSame('environment-test-5', $normalizer->normalize('environment-test'));
        $this->assertSame('environment-test-6', $normalizer->normalize('environment-test'));
        $this->assertSame('environment-test-7', $normalizer->normalize('environment-test'));
    }
}
