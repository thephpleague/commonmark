<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 * (c) Rezo Zero / Ambroise Maupate
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace League\CommonMark\Extension\Footnote\Parser;

use League\CommonMark\Configuration\ConfigurationAwareInterface;
use League\CommonMark\Configuration\ConfigurationInterface;
use League\CommonMark\Extension\Footnote\Node\FootnoteRef;
use League\CommonMark\Normalizer\SlugNormalizer;
use League\CommonMark\Normalizer\TextNormalizerInterface;
use League\CommonMark\Parser\Inline\InlineParserInterface;
use League\CommonMark\Parser\Inline\InlineParserMatch;
use League\CommonMark\Parser\InlineParserContext;
use League\CommonMark\Reference\Reference;

final class AnonymousFootnoteRefParser implements InlineParserInterface, ConfigurationAwareInterface
{
    /** @var ConfigurationInterface */
    private $config;

    /**
     * @var TextNormalizerInterface
     *
     * @psalm-readonly
     */
    private $slugNormalizer;

    public function __construct()
    {
        $this->slugNormalizer = new SlugNormalizer();
    }

    public function getMatchDefinition(): InlineParserMatch
    {
        return InlineParserMatch::regex('\^\[[^\]]+\]');
    }

    public function parse(string $match, InlineParserContext $inlineContext): bool
    {
        if (\preg_match('#\^\[([^\]]+)\]#', $match, $matches) <= 0) {
            return false;
        }

        $inlineContext->getCursor()->advanceBy(\mb_strlen($match));

        $reference = $this->createReference($matches[1]);
        $inlineContext->getContainer()->appendChild(new FootnoteRef($reference, $matches[1]));

        return true;
    }

    /**
     * @psalm-immutable
     */
    private function createReference(string $label): Reference
    {
        $refLabel = $this->slugNormalizer->normalize($label);
        $refLabel = \mb_substr($refLabel, 0, 20);

        return new Reference(
            $refLabel,
            '#' . $this->config->get('footnote/footnote_id_prefix', 'fn:') . $refLabel,
            $label
        );
    }

    public function setConfiguration(ConfigurationInterface $config): void
    {
        $this->config = $config;
    }
}
