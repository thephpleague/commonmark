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
use PHPUnit\Framework\TestCase;

final class NormalizeHeadingsProcessorTest extends TestCase
{
    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testClampsHeadingLevelsWithinConfiguredRange(): void
    {
        $processor = new NormalizeHeadingsProcessor();
        $processor->setEnvironment($this->createEnvironment([
            'normalize_headings' => [
                'min_level' => 2,
                'max_level' => 4,
            ],
        ]));

        $document = new Document();
        foreach ([1, 2, 3, 4, 5, 6] as $level) {
            $document->appendChild(new Heading($level));
        }

        $processor(new DocumentParsedEvent($document));

        $levels = [];
        foreach ($document->iterator(NodeIterator::FLAG_BLOCKS_ONLY) as $node) {
            if ($node instanceof Heading) {
                $levels[] = $node->getLevel();
            }
        }

        $this->assertSame([2, 2, 3, 4, 4, 4], $levels);
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testLeavesHeadingsUntouchedWithDefaultConfig(): void
    {
        $processor = new NormalizeHeadingsProcessor();
        $processor->setEnvironment($this->createEnvironment());

        $document = new Document();
        foreach ([1, 2, 3, 4, 5, 6] as $level) {
            $document->appendChild(new Heading($level));
        }

        $processor(new DocumentParsedEvent($document));

        $levels = [];
        foreach ($document->iterator(NodeIterator::FLAG_BLOCKS_ONLY) as $node) {
            if ($node instanceof Heading) {
                $levels[] = $node->getLevel();
            }
        }

        $this->assertSame([1, 2, 3, 4, 5, 6], $levels);
    }

    public function testThrowsExceptionWhenMinLevelExceedsMaxLevel(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $processor = new NormalizeHeadingsProcessor();
        $processor->setEnvironment($this->createEnvironment([
            'normalize_headings' => [
                'min_level' => 4,
                'max_level' => 2,
            ],
        ]));

        $document = new Document();
        $document->appendChild(new Heading(3));

        $processor(new DocumentParsedEvent($document));
    }

    /**
     * @param array<string, mixed> $values
     */
    private function createEnvironment(array $values = []): EnvironmentInterface
    {
        $environment = new Environment($values);
        $environment->addExtension(new NormalizeHeadingsExtension());

        return $environment;
    }
}
