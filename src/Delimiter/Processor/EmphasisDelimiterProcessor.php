<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (https://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * Additional emphasis processing code based on commonmark-java (https://github.com/atlassian/commonmark-java)
 *  - (c) Atlassian Pty Ltd
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Delimiter\Processor;

use League\CommonMark\Delimiter\Delimiter;
use League\CommonMark\Inline\Element\Emphasis;
use League\CommonMark\Inline\Element\Strong;
use League\CommonMark\Inline\Element\Text;
use League\CommonMark\Util\Configuration;
use League\CommonMark\Util\ConfigurationAwareInterface;

final class EmphasisDelimiterProcessor implements DelimiterProcessorInterface, ConfigurationAwareInterface
{
    /** @var string */
    private $char;

    /** @var Configuration|null */
    private $config;

    /**
     * @param string $char The emphasis character to use (typically '*' or '_')
     */
    public function __construct(string $char)
    {
        $this->char = $char;
    }

    /**
     * {@inheritdoc}
     */
    public function getOpeningCharacter(): string
    {
        return $this->char;
    }

    /**
     * {@inheritdoc}
     */
    public function getClosingCharacter(): string
    {
        return $this->char;
    }

    /**
     * {@inheritdoc}
     */
    public function getMinLength(): int
    {
        return 1;
    }

    /**
     * {@inheritdoc}
     */
    public function getDelimiterUse(Delimiter $opener, Delimiter $closer): int
    {
        // "Multiple of 3" rule for internal delimiter runs
        if (($opener->canClose() || $closer->canOpen()) && $closer->getOrigDelims() % 3 !== 0 && ($opener->getOrigDelims() + $closer->getOrigDelims()) % 3 === 0) {
            return 0;
        }

        // Calculate actual number of delimiters used from this closer
        if ($opener->getNumDelims() >= 2 && $closer->getNumDelims() >= 2) {
            if ($this->config && $this->config->getConfig('enable_strong', true)) {
                return 2;
            }

            return 0;
        }

        if ($this->config && $this->config->getConfig('enable_em', true)) {
            return 1;
        }

        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function process(Text $opener, Text $closer, int $delimiterUse)
    {
        if ($delimiterUse === 1) {
            $emphasis = new Emphasis();
        } elseif ($delimiterUse === 2) {
            $emphasis = new Strong();
        } else {
            return;
        }

        $tmp = $opener->next();
        while ($tmp !== null && $tmp !== $closer) {
            $next = $tmp->next();
            $emphasis->appendChild($tmp);
            $tmp = $next;
        }

        $opener->insertAfter($emphasis);
    }

    /**
     * @param Configuration $configuration
     */
    public function setConfiguration(Configuration $configuration)
    {
        $this->config = $configuration;
    }
}
