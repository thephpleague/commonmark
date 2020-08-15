<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace League\CommonMark\Normalizer;

final class UniqueSlugNormalizer implements TextNormalizerInterface
{
    /** @var SlugNormalizer */
    private $slugNormalizer;

    /** @var array<string, int> */
    private $usages;

    public function __construct()
    {
        $this->slugNormalizer = new SlugNormalizer();
    }

    public function normalize(string $text, $context = null): string
    {
        $slug = $this->slugNormalizer->normalize($text, $context);

        if (!isset($this->usages[$slug])) {
            $this->usages[$slug] = 1;

            return $slug;
        }

        $count = $this->usages[$slug]++;

        return $slug . '-' . $count;
    }
}
