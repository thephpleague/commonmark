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

// phpcs:disable Squiz.Strings.DoubleQuoteUsage.ContainsVar
final class UniqueSlugNormalizer implements UniqueSlugNormalizerInterface
{
    private TextNormalizerInterface $innerNormalizer;
    /** @var array<string, bool> */
    private array $alreadyUsed = [];
    /** @var array<string, bool> */
    private array $blacklist = [];

    public function __construct(TextNormalizerInterface $innerNormalizer)
    {
        $this->innerNormalizer = $innerNormalizer;
    }

    /**
     * Set a list of IDs that should be treated as already used
     *
     * These IDs will be considered "taken" and any generated slug matching
     * a blacklisted ID will receive a numeric suffix (e.g., -1, -2, etc.)
     *
     * @param array<string> $blacklistedIds List of IDs to blacklist
     */
    public function setBlacklist(array $blacklistedIds): void
    {
        $this->blacklist = [];
        foreach ($blacklistedIds as $id) {
            // Normalize the blacklist entry using the same normalizer as headings
            $normalized = $this->innerNormalizer->normalize($id);
            if ($normalized !== '') {
                $this->blacklist[$normalized] = true;
            }
        }
        $this->clearHistory();
    }

    public function clearHistory(): void
    {
        $this->alreadyUsed = $this->blacklist;
    }


    /**
     * {@inheritDoc}
     *
     * @psalm-allow-private-mutation
     */
    public function normalize(string $text, array $context = []): string
    {
        $normalized = $this->innerNormalizer->normalize($text, $context);

        // If it's not unique, add an incremental number to the end until we get a unique version
        if (\array_key_exists($normalized, $this->alreadyUsed)) {
            $suffix = 0;
            do {
                ++$suffix;
            } while (\array_key_exists("$normalized-$suffix", $this->alreadyUsed));

            $normalized = "$normalized-$suffix";
        }

        $this->alreadyUsed[$normalized] = true;

        return $normalized;
    }
}
