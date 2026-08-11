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

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\CommonMark\Node\Block\BlockQuote;
use League\CommonMark\Extension\CommonMark\Node\Block\FencedCode;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Extension\DefaultAttributes\DefaultAttributesExtension;
use League\CommonMark\Extension\Table\Table;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\MarkdownConverter;
use League\CommonMark\Node\Block\Paragraph;
use League\CommonMark\Node\Node;
use League\Config\Exception\InvalidConfigurationException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class DefaultAttributesExtensionTest extends TestCase
{
    private const LINK_MARKDOWN = '[league/commonmark](https://commonmark.thephpleague.com)';

    /**
     * @dataProvider provideTestCases
     *
     * @param array<string, mixed> $config
     */
    #[DataProvider('provideTestCases')]
    public function testExample(string $markdown, array $config, string $expectedHtml): void
    {
        $environment = new Environment($config);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new TableExtension());
        $environment->addExtension(new DefaultAttributesExtension());
        $converter = new MarkdownConverter($environment);

        $this->assertSame($expectedHtml, \rtrim($converter->convert($markdown)->getContent()));
    }

    /**
     * Every value form, in both callable-handling modes.
     *
     * @dataProvider provideValueForms
     *
     * @param mixed $value
     */
    #[DataProvider('provideValueForms')]
    public function testValueForm($value, bool $strictCallables, string $expectedClass): void
    {
        $html = $this->convertLink($value, $strictCallables);

        $this->assertSame(
            \sprintf('<p><a class="%s" href="https://commonmark.thephpleague.com">league/commonmark</a></p>', $expectedClass),
            $html
        );
    }

    /**
     * @return iterable<string, array{mixed, bool, string}>
     */
    public static function provideValueForms(): iterable
    {
        $staticMethod = AttributeCallbacks::class . '::cssClass';

        // Strings which happen to name a PHP function are values, not callbacks (#1123)
        foreach (['link', 'header', 'key', 'range', 'current'] as $collision) {
            yield 'strict: "' . $collision . '"' => [$collision, true, $collision];
        }

        yield 'strict: multi-word string'   => ['btn btn-link', true, 'btn btn-link'];
        yield 'strict: string[]'            => [['btn', 'btn-primary'], true, 'btn btn-primary'];
        yield 'strict: static method string' => [$staticMethod, true, $staticMethod];
        yield 'strict: array callable'      => [[AttributeCallbacks::class, 'cssClass'], true, AttributeCallbacks::class . ' cssClass'];
        yield 'strict: closure'             => [static fn (Node $node): string => 'from-closure', true, 'from-closure'];
        yield 'strict: fromCallable'        => [\Closure::fromCallable([AttributeCallbacks::class, 'cssClass']), true, 'from-static-method'];
        yield 'strict: invokable object'    => [new AttributeCallbacks(), true, 'from-invokable'];

        yield 'legacy: multi-word string'   => ['btn btn-link', false, 'btn btn-link'];
        yield 'legacy: string[]'            => [['btn', 'btn-primary'], false, 'btn btn-primary'];
        yield 'legacy: static method string' => [$staticMethod, false, 'from-static-method'];
        yield 'legacy: array callable'      => [[AttributeCallbacks::class, 'cssClass'], false, 'from-static-method'];
        yield 'legacy: closure'             => [static fn (Node $node): string => 'from-closure', false, 'from-closure'];
        yield 'legacy: fromCallable'        => [\Closure::fromCallable([AttributeCallbacks::class, 'cssClass']), false, 'from-static-method'];
        yield 'legacy: invokable object'    => [new AttributeCallbacks(), false, 'from-invokable'];
    }

    /**
     * Legacy mode still invokes string callables, but a value which collided with an unrelated function
     * should name the configuration responsible rather than failing inside that function.
     *
     * @dataProvider provideCollidingStrings
     */
    #[DataProvider('provideCollidingStrings')]
    public function testCollidingStringInLegacyModeNamesTheConfiguration(string $value): void
    {
        // Only the wording this library controls is asserted here; PHP's own message for the collided
        // function differs between versions, and is covered by the previous-exception test below
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage(\sprintf(
            'The "class" attribute configured for %s is the string "%s", which PHP also treats as a callable, '
            . 'so it was called with the node and failed:',
            Link::class,
            $value
        ));

        $this->convertLink($value, false);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideCollidingStrings(): iterable
    {
        yield 'wrong argument count' => ['link'];
        yield 'wrong argument type'  => ['header'];
    }

    /**
     * The underlying error is preserved so the original cause is never lost.
     */
    public function testCollidingStringExceptionKeepsTheOriginalError(): void
    {
        try {
            $this->convertLink('link', false);
            $this->fail('Expected an ' . InvalidConfigurationException::class);
        } catch (InvalidConfigurationException $e) {
            $previous = $e->getPrevious();

            $this->assertInstanceOf(\ArgumentCountError::class, $previous);
            $this->assertStringContainsString($previous->getMessage(), $e->getMessage());
        }
    }

    /**
     * Only values which actually get invoked can fail, so a collision configured for a node type the
     * document never contains stays inert.
     */
    public function testCollidingStringIsInertWhenNoSuchNodeExists(): void
    {
        $environment = new Environment(['default_attributes' => [Table::class => ['class' => 'link']]]);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new TableExtension());
        $environment->addExtension(new DefaultAttributesExtension());

        $this->assertSame(
            '<p>no tables here</p>',
            \rtrim((new MarkdownConverter($environment))->convert('no tables here')->getContent())
        );
    }

    /**
     * Strict mode makes no exception for arrays which happen to be callable, so enabling it produces
     * exactly the behavior 3.0 will have.
     */
    public function testEveryArrayIsLiteralInStrictMode(): void
    {
        $this->assertSame(
            '<p><a class="' . AttributeCallbacks::class . ' cssClass" href="https://commonmark.thephpleague.com">league/commonmark</a></p>',
            $this->convertLink([AttributeCallbacks::class, 'cssClass'], true)
        );
    }

    /**
     * @dataProvider provideBothModes
     */
    #[DataProvider('provideBothModes')]
    public function testBooleanValue(bool $strictCallables): void
    {
        $environment = new Environment([
            'default_attributes' => [
                'attributes' => [Link::class => ['data-tracked' => true]],
                'strict_callables' => $strictCallables,
            ],
        ]);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new DefaultAttributesExtension());

        $this->assertSame(
            '<p><a data-tracked href="https://commonmark.thephpleague.com">league/commonmark</a></p>',
            \rtrim((new MarkdownConverter($environment))->convert(self::LINK_MARKDOWN)->getContent())
        );
    }

    /**
     * @return iterable<string, array{bool}>
     */
    public static function provideBothModes(): iterable
    {
        yield 'strict' => [true];
        yield 'legacy' => [false];
    }

    /**
     * The upgrade path for someone already using the flat shape: keep it, add the toggle.
     */
    public function testFlatShapeWithToggleAppliesStrictHandling(): void
    {
        $environment = new Environment([
            'default_attributes' => [
                Link::class => ['class' => 'link'],
                'strict_callables' => true,
            ],
        ]);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new DefaultAttributesExtension());

        $this->assertSame(
            '<p><a class="link" href="https://commonmark.thephpleague.com">league/commonmark</a></p>',
            \rtrim((new MarkdownConverter($environment))->convert(self::LINK_MARKDOWN)->getContent())
        );
    }

    /**
     * A callback typed against an interface the configured node implements is still a valid callback.
     */
    public function testLegacyCallbackTypedAgainstAnImplementedInterfaceIsAccepted(): void
    {
        $environment = new Environment([
            'default_attributes' => [
                FencedCode::class => ['class' => AttributeCallbacks::class . '::stringContainerClass'],
            ],
        ]);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new DefaultAttributesExtension());

        $this->assertSame(
            "<pre><code class=\"from-interface-typed\">hi\n</code></pre>",
            \rtrim((new MarkdownConverter($environment))->convert("```\nhi\n```")->getContent())
        );
    }

    /**
     * @param mixed $value
     */
    private function convertLink($value, bool $strictCallables): string
    {
        $environment = new Environment([
            'default_attributes' => [
                'attributes' => [Link::class => ['class' => $value]],
                'strict_callables' => $strictCallables,
            ],
        ]);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new DefaultAttributesExtension());

        return \rtrim((new MarkdownConverter($environment))->convert(self::LINK_MARKDOWN)->getContent());
    }

    /**
     * @return iterable<mixed>
     */
    public static function provideTestCases(): iterable
    {
        $markdown = <<<MD
# Hello World

Welcome to my blog!

## About Me

I'm a nerd who **loves** the [league/commonmark](https://commonmark.thephpleague.com) library.
MD;

        yield [
            $markdown,
            [],
            <<<HTML
<h1>Hello World</h1>
<p>Welcome to my blog!</p>
<h2>About Me</h2>
<p>I'm a nerd who <strong>loves</strong> the <a href="https://commonmark.thephpleague.com">league/commonmark</a> library.</p>
HTML,
        ];

        yield [
            $markdown,
            [
                'default_attributes' => [],
            ],
            <<<HTML
<h1>Hello World</h1>
<p>Welcome to my blog!</p>
<h2>About Me</h2>
<p>I'm a nerd who <strong>loves</strong> the <a href="https://commonmark.thephpleague.com">league/commonmark</a> library.</p>
HTML,
        ];

        yield [
            $markdown,
            [
                'default_attributes' => [
                    Paragraph::class => [
                        'class' => ['text-center', 'font-comic-sans'],
                    ],
                    Link::class => [
                        'class' => 'btn btn-link',
                        'target' => '_blank',
                    ],
                    Heading::class => [
                        'class' => static function (Heading $node) {
                            if ($node->getLevel() === 1) {
                                return 'page-title';
                            }

                            return null;
                        },
                    ],
                ],
            ],
            <<<HTML
<h1 class="page-title">Hello World</h1>
<p class="text-center font-comic-sans">Welcome to my blog!</p>
<h2>About Me</h2>
<p class="text-center font-comic-sans">I'm a nerd who <strong>loves</strong> the <a class="btn btn-link" target="_blank" href="https://commonmark.thephpleague.com" rel="noopener noreferrer">league/commonmark</a> library.</p>
HTML,
        ];

        // One last test to theme some Bootstrap 4 content
        yield [
            <<<MD
# U.S. National Parks

The United States has some amazing national parks.

As William Shakespeare once said:

> One touch of nature makes the whole world kin.

## My Favorites

| Park        | Location                | Established       |
| ----------- | ----------------------- | ----------------- |
| Yellowstone | Montana, Wyoming, Idaho | March 1, 1872     |
| Yosemite    | California              | March 1, 1872     |
| Zion        | Utah                    | November 19, 1919 |
MD
,
            [
                'default_attributes' => [
                    Table::class => [
                        'class' => ['table', 'table-responsive'],
                    ],
                    BlockQuote::class => [
                        'class' => 'blockquote',
                    ],
                    Paragraph::class => [
                        'class' => static function (Paragraph $paragraph) {
                            if ($paragraph->previous() instanceof Heading && $paragraph->previous()->getLevel() === 1) {
                                return 'lead';
                            }

                            return null;
                        },
                    ],
                ],
            ],
            <<<HTML
<h1>U.S. National Parks</h1>
<p class="lead">The United States has some amazing national parks.</p>
<p>As William Shakespeare once said:</p>
<blockquote class="blockquote">
<p>One touch of nature makes the whole world kin.</p>
</blockquote>
<h2>My Favorites</h2>
<table class="table table-responsive">
<thead>
<tr>
<th>Park</th>
<th>Location</th>
<th>Established</th>
</tr>
</thead>
<tbody>
<tr>
<td>Yellowstone</td>
<td>Montana, Wyoming, Idaho</td>
<td>March 1, 1872</td>
</tr>
<tr>
<td>Yosemite</td>
<td>California</td>
<td>March 1, 1872</td>
</tr>
<tr>
<td>Zion</td>
<td>Utah</td>
<td>November 19, 1919</td>
</tr>
</tbody>
</table>
HTML,
        ];
    }
}
