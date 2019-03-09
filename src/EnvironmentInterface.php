<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark;

use League\CommonMark\Block\Parser\BlockParserInterface;
use League\CommonMark\Block\Renderer\BlockRendererInterface;
use League\CommonMark\Inline\Parser\InlineParserInterface;
use League\CommonMark\Inline\Processor\InlineProcessorInterface;
use League\CommonMark\Inline\Renderer\InlineRendererInterface;

interface EnvironmentInterface
{
    const HTML_INPUT_STRIP = 'strip';
    const HTML_INPUT_ALLOW = 'allow';
    const HTML_INPUT_ESCAPE = 'escape';

    /**
     * @param string|null $key
     * @param mixed       $default
     *
     * @return mixed
     */
    public function getConfig($key = null, $default = null);

    /**
     * @return \Traversable<BlockParserInterface>
     */
    public function getBlockParsers();

    /**
     * @param string $character
     *
     * @return \Traversable<InlineParserInterface>
     */
    public function getInlineParsersForCharacter($character);

    /**
     * @return \Traversable<InlineProcessorInterface>
     */
    public function getInlineProcessors();

    /**
     * @return \Traversable<DocumentProcessorInterface>
     */
    public function getDocumentProcessors();

    /**
     * @param string $blockClass
     *
     * @return BlockRendererInterface|null
     */
    public function getBlockRendererForClass($blockClass);

    /**
     * @param string $inlineClass
     *
     * @return InlineRendererInterface|null
     */
    public function getInlineRendererForClass($inlineClass);

    /**
     * Regex which matches any character which doesn't indicate an inline element
     *
     * This allows us to parse multiple non-special characters at once
     *
     * @return string
     */
    public function getInlineParserCharacterRegex();
}
