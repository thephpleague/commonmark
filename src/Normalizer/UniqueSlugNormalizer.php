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

namespace League\CommonMark\Normalizer;

use League\CommonMark\Event\DocumentRenderedEvent;

// phpcs:disable Squiz.Strings.DoubleQuoteUsage.ContainsVar
final class UniqueSlugNormalizer implements TextNormalizerInterface
{
    public const SCOPE_ENVIRONMENT = 'environment';
    public const SCOPE_DOCUMENT    = 'document';

    /** @var TextNormalizerInterface */
    private $innerNormalizer;
    /** @psalm-var self::SCOPE_* */
    private $scope;
    /** @var array<string, bool> */
    private $alreadyUsed = [];

    /**
     * @psalm-param self::SCOPE_* $scope
     */
    public function __construct(TextNormalizerInterface $innerNormalizer, string $scope = self::SCOPE_DOCUMENT)
    {
        $this->innerNormalizer = $innerNormalizer;
        $this->scope           = $scope;
    }

    /**
     * @internal
     */
    public function onDocumentRendered(DocumentRenderedEvent $event): void
    {
        if ($this->scope === self::SCOPE_DOCUMENT) {
            $this->alreadyUsed = [];
        }
    }

    /**
     * {@inheritDoc}
     */
    public function normalize(string $text, array $context = []): string
    {
        $normalized = $this->innerNormalizer->normalize($text, $context);

        // If it's not unique, add an incremental number to the end until we get a unique version
        if (\array_key_exists($normalized, $this->alreadyUsed)) {
            $extension = 0;
            do {
                ++$extension;
            } while (\array_key_exists("$normalized-$extension", $this->alreadyUsed));

            $normalized = "$normalized-$extension";
        }

        $this->alreadyUsed[$normalized] = true;

        return $normalized;
    }
}
