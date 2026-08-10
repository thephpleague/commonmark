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

namespace League\CommonMark\Tests\Functional\Extension\DefaultAttributes;

use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Extension\DefaultAttributes\DefaultAttributesExtension;
use League\CommonMark\Node\Block\Paragraph;
use League\Config\Configuration;
use League\Config\Exception\ValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * The `default_attributes` option accepts both a flat map of node class => attributes and a structure
 * which pairs that map with the `strict_callables` toggle. Both are normalized to the structure.
 */
final class DefaultAttributesConfigurationTest extends TestCase
{
    /**
     * @dataProvider provideShapes
     *
     * @param array<int, array<string, mixed>>    $merges
     * @param array<string, array<string, mixed>> $expectedAttributes
     */
    #[DataProvider('provideShapes')]
    public function testShapeIsNormalized(array $merges, array $expectedAttributes, bool $expectedStrictCallables): void
    {
        $config = $this->buildConfiguration($merges);

        $this->assertSame($expectedAttributes, $config->get('default_attributes/attributes'));
        $this->assertSame($expectedStrictCallables, $config->get('default_attributes/strict_callables'));
    }

    /**
     * @return iterable<string, array{array<int, array<string, mixed>>, array<string, array<string, mixed>>, bool}>
     */
    public static function provideShapes(): iterable
    {
        $link      = [Link::class => ['class' => 'btn']];
        $paragraph = [Paragraph::class => ['class' => 'lead']];

        yield 'option absent' => [[], [], true];

        yield 'flat map' => [
            [['default_attributes' => $link]],
            $link,
            false,
        ];

        yield 'structure with toggle' => [
            [['default_attributes' => ['attributes' => $link, 'strict_callables' => true]]],
            $link,
            true,
        ];

        yield 'structure without toggle' => [
            [['default_attributes' => ['attributes' => $link]]],
            $link,
            true,
        ];

        yield 'empty array' => [
            [['default_attributes' => []]],
            [],
            true,
        ];

        yield 'toggle without attributes' => [
            [['default_attributes' => ['strict_callables' => true]]],
            [],
            true,
        ];

        // The raw config is accumulated across merges and only validated on read, so both shapes can
        // arrive at the normalizer as a single array holding node classes alongside reserved keys
        yield 'flat map merged with toggle' => [
            [
                ['default_attributes' => $link],
                ['default_attributes' => ['strict_callables' => true]],
            ],
            $link,
            true,
        ];

        yield 'flat map and toggle in one array' => [
            [['default_attributes' => $link + ['strict_callables' => true]]],
            $link,
            true,
        ];

        yield 'two flat maps' => [
            [
                ['default_attributes' => $link],
                ['default_attributes' => $paragraph],
            ],
            $link + $paragraph,
            false,
        ];

        yield 'flat map merged with structure' => [
            [
                ['default_attributes' => $link],
                ['default_attributes' => ['attributes' => $paragraph]],
            ],
            $link + $paragraph,
            false,
        ];
    }

    public function testExplicitAttributesWinOverStrayFlatKeys(): void
    {
        $config = $this->buildConfiguration([
            ['default_attributes' => [Link::class => ['class' => 'from-flat']]],
            ['default_attributes' => ['attributes' => [Link::class => ['class' => 'from-structure']]]],
        ]);

        $this->assertSame(
            [Link::class => ['class' => 'from-structure']],
            $config->get('default_attributes/attributes')
        );
    }

    public function testInvalidAttributeMapIsRejected(): void
    {
        $config = $this->buildConfiguration([
            ['default_attributes' => [Link::class => 'not-an-array']],
        ]);

        $this->expectException(ValidationException::class);
        // The path separators are surrounded by non-breaking spaces
        $this->expectExceptionMessageMatches(
            '/default_attributes\W+attributes\W+' . \preg_quote(Link::class, '/') . "' expects to be array/"
        );

        $config->get('default_attributes');
    }

    /**
     * An `attributes` which isn't an array has nothing to merge node classes into, and must still be
     * reported by the configuration layer rather than failing inside the normalizer.
     *
     * @dataProvider provideInvalidAttributesValues
     *
     * @param array<string, mixed> $merge
     */
    #[DataProvider('provideInvalidAttributesValues')]
    public function testInvalidAttributesValueIsRejected(array $merge): void
    {
        $config = $this->buildConfiguration([['default_attributes' => $merge]]);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessageMatches('/default_attributes\W+attributes\' expects to be array/');

        $config->get('default_attributes');
    }

    /**
     * @return iterable<string, array{array<string, mixed>}>
     */
    public static function provideInvalidAttributesValues(): iterable
    {
        yield 'on its own'              => [['attributes' => 'not-an-array']];
        yield 'alongside a node class'  => [['attributes' => 'not-an-array', Link::class => ['class' => 'btn']]];
    }

    /**
     * @param array<int, array<string, mixed>> $merges
     */
    private function buildConfiguration(array $merges): Configuration
    {
        $config = new Configuration();
        (new DefaultAttributesExtension())->configureSchema($config);

        foreach ($merges as $merge) {
            $config->merge($merge);
        }

        return $config;
    }
}
