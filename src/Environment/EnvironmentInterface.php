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

namespace League\CommonMark\Environment;

use League\CommonMark\Delimiter\Processor\DelimiterProcessorCollection;
use League\CommonMark\Parser\Block\BlockStartParserInterface;
use League\CommonMark\Parser\Inline\InlineParserInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

interface EnvironmentInterface extends EventDispatcherInterface
{
    /**
     * @param string $key     Configuration option key
     * @param mixed  $default Default value to return if config option is not set
     *
     * @return mixed
     */
    public function getConfig(string $key, $default = null);

    /**
     * @return iterable<BlockStartParserInterface>
     */
    public function getBlockStartParsers(): iterable;

    /**
     * @return iterable<InlineParserInterface>
     */
    public function getInlineParsers(): iterable;

    public function getDelimiterProcessors(): DelimiterProcessorCollection;

    /**
     * @psalm-param class-string $nodeClass
     *
     * @return iterable<NodeRendererInterface>
     */
    public function getRenderersForClass(string $nodeClass): iterable;
}
