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
use League\CommonMark\Event\AbstractEvent;
use League\CommonMark\Parser\Block\BlockStartParserInterface;
use League\CommonMark\Parser\Inline\InlineParserInterface;
use League\CommonMark\Renderer\NodeRendererInterface;

interface EnvironmentInterface
{
    /**
     * @param string|null $key     Configuration option key
     * @param mixed       $default Default value to return if config option is not set
     *
     * @return mixed
     */
    public function getConfig(?string $key = null, $default = null);

    /**
     * @return iterable<BlockStartParserInterface>
     */
    public function getBlockStartParsers(): iterable;

    /**
     * @return iterable<InlineParserInterface>
     */
    public function getInlineParsersForCharacter(string $character): iterable;

    public function getDelimiterProcessors(): DelimiterProcessorCollection;

    /**
     * @return iterable<NodeRendererInterface>
     *
     * @psalm-param class-string $nodeClass
     */
    public function getRenderersForClass(string $nodeClass): iterable;

    /**
     * Regex which matches any character which doesn't indicate an inline element
     *
     * This allows us to parse multiple non-special characters at once
     */
    public function getInlineParserCharacterRegex(): string;

    /**
     * Dispatches the given event to listeners
     */
    public function dispatch(AbstractEvent $event): void;
}
