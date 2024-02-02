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

namespace League\CommonMark\Tests\Unit\Extension\FrontMatter\Data;

use League\CommonMark\Extension\FrontMatter\Data\SymfonyYamlFrontMatterParser;
use League\CommonMark\Extension\FrontMatter\Exception\InvalidFrontMatterException;
use PHPUnit\Framework\TestCase;
use PackageVersions\Versions as InstalledComposerPackages;

final class SymfonyYamlFrontMatterParserTest extends TestCase
{
    /**
     * @dataProvider provideValidYamlExamples
     *
     * @param mixed $expected
     */
    public function testParseWithValidYaml(string $input, $expected): void
    {
        $dataParser = new SymfonyYamlFrontMatterParser();

        $this->assertSame($expected, $dataParser->parse($input));
    }

    /**
     * @return iterable<mixed>
     */
    public static function provideValidYamlExamples(): iterable
    {
        yield ['Hello, World!', 'Hello, World!'];
        yield ["- 1\n- 2\n- 3", [1, 2, 3]];
        yield ["foo: bar\nbaz: 42", ['foo' => 'bar', 'baz' => 42]];
    }

    /**
     * @dataProvider provideInvalidYamlExamples
     */
    public function testParseWithInvalidYaml(string $input): void
    {
        $this->expectException(InvalidFrontMatterException::class);

        $dataParser = new SymfonyYamlFrontMatterParser();

        $dataParser->parse($input);
    }

    /**
     * @return iterable<string>
     */
    public static function provideInvalidYamlExamples(): iterable
    {
        yield ["this:\n    is:invalid\n        yaml: data"];

        if (! self::yamlLibrarySupportsObjectUnserialization()) {
            yield ['foo: !php/object:O:30:"Symfony\Tests\Component\Yaml\B":1:{s:1:"b";s:3:"foo";}'];
        }
    }

    private static function yamlLibrarySupportsObjectUnserialization(): bool
    {
        $fullVersion = InstalledComposerPackages::getVersion('symfony/yaml');
        \preg_match('/^v?([^@]+)/', $fullVersion, $matches);

        return \version_compare($matches[1], '4.1.0', '<');
    }
}
