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

namespace League\CommonMark\Tests\Unit\Extension\NormalizeHeadings;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Environment\EnvironmentInterface;
use League\CommonMark\Event\DocumentParsedEvent;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Extension\NormalizeHeadings\NormalizeHeadingsExtension;
use League\CommonMark\Extension\NormalizeHeadings\NormalizeHeadingsProcessor;
use League\CommonMark\Node\Block\Document;
use League\CommonMark\Node\NodeIterator;
use League\Config\Exception\InvalidConfigurationException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class NormalizeHeadingsProcessorTest extends TestCase
{
    /**
     * @dataProvider provideNormalizationCases
     *
     * @param array<string, mixed> $config
     * @param int[]                $levels
     * @param int[]                $expected
     *
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    #[DataProvider('provideNormalizationCases')]
    public function testNormalization(array $config, array $levels, array $expected): void
    {
        $this->assertSame($expected, $this->process($config, $levels));
    }

    /**
     * @return iterable<string, array{array<string, mixed>, int[], int[]}>
     */
    public static function provideNormalizationCases(): iterable
    {
        yield 'skipped levels are repaired, keeping siblings together' => [[], [1, 4, 4, 4, 1, 3], [1, 2, 2, 2, 1, 2]];

        yield 'already-valid headings are left alone' => [[], [1, 2, 3, 4, 5, 6], [1, 2, 3, 4, 5, 6]];

        yield 'a deep first heading stays where the author put it' => [[], [3, 5], [3, 4]];

        yield 'headings are clamped to the configured range' => [
            ['min_level' => 2, 'max_level' => 4],
            [1, 2, 3, 4, 5, 6],
            [2, 3, 4, 4, 4, 4],
        ];

        yield 'headings deeper than max_level collapse into siblings' => [
            ['max_level' => 2],
            [1, 2, 3, 4],
            [1, 2, 2, 2],
        ];

        yield 'an identical min and max flattens every heading' => [
            ['min_level' => 2, 'max_level' => 2],
            [1, 2, 3],
            [2, 2, 2],
        ];

        yield 'rebasing moves a deep document up to min_level' => [
            ['rebase_to_min_level' => true],
            [3, 5],
            [1, 2],
        ];

        yield 'rebasing moves a shallow document down to min_level' => [
            ['min_level' => 2, 'rebase_to_min_level' => true],
            [1, 2, 3],
            [2, 3, 4],
        ];

        yield 'rebasing applies to every top-level heading, not just the first' => [
            ['rebase_to_min_level' => true],
            [2, 4, 2],
            [1, 2, 1],
        ];
    }

    public function testThrowsExceptionWhenMinLevelExceedsMaxLevel(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $this->process(['min_level' => 4, 'max_level' => 2], [3]);
    }

    /**
     * Runs the processor over a document containing headings of the given levels, returning their new levels
     *
     * @param array<string, mixed> $config
     * @param int[]                $levels
     *
     * @return int[]
     */
    private function process(array $config, array $levels): array
    {
        $processor = new NormalizeHeadingsProcessor();
        $processor->setEnvironment($this->createEnvironment($config));

        $document = new Document();
        foreach ($levels as $level) {
            $document->appendChild(new Heading($level));
        }

        $processor(new DocumentParsedEvent($document));

        $newLevels = [];
        foreach ($document->iterator(NodeIterator::FLAG_BLOCKS_ONLY) as $node) {
            if ($node instanceof Heading) {
                $newLevels[] = $node->getLevel();
            }
        }

        return $newLevels;
    }

    /**
     * @param array<string, mixed> $config
     */
    private function createEnvironment(array $config): EnvironmentInterface
    {
        $environment = new Environment($config === [] ? [] : ['normalize_headings' => $config]);
        $environment->addExtension(new NormalizeHeadingsExtension());

        return $environment;
    }
}
