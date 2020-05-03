<?php

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
     * @param string|null $key
     * @param mixed       $default
     *
     * @return mixed
     */
    public function getConfig($key = null, $default = null);

    /**
     * @return iterable<BlockStartParserInterface>
     */
    public function getBlockStartParsers(): iterable;

    /**
     * @param string $character
     *
     * @return iterable<InlineParserInterface>
     */
    public function getInlineParsersForCharacter(string $character): iterable;

    /**
     * @return DelimiterProcessorCollection
     */
    public function getDelimiterProcessors(): DelimiterProcessorCollection;

    /**
     * @param string $nodeClass
     *
     * @return iterable<NodeRendererInterface>
     */
    public function getRenderersForClass(string $nodeClass): iterable;

    /**
     * Regex which matches any character which doesn't indicate an inline element
     *
     * This allows us to parse multiple non-special characters at once
     *
     * @return string
     */
    public function getInlineParserCharacterRegex(): string;

    /**
     * Dispatches the given event to listeners
     *
     * @param AbstractEvent $event
     *
     * @return void
     */
    public function dispatch(AbstractEvent $event): void;
}
