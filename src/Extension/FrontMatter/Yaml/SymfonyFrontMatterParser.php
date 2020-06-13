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

namespace League\CommonMark\Extension\FrontMatter\Yaml;

use Symfony\Component\Yaml\Yaml;

final class SymfonyFrontMatterParser implements FrontMatterParserInterface
{
    /**
     * {@inheritDoc}
     */
    public function parse(string $frontMatter)
    {
        if (! \class_exists(Yaml::class)) {
            throw new \RuntimeException('Failed to parse yaml: "symfony/yaml" library is missing');
        }

        return Yaml::parse($frontMatter);
    }
}
