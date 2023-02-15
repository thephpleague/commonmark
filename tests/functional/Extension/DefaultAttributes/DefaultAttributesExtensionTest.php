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
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\Extension\DefaultAttributes\DefaultAttributesExtension;
use League\CommonMark\Extension\Table\Table;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\MarkdownConverter;
use League\CommonMark\Node\Block\Paragraph;
use PHPUnit\Framework\TestCase;

final class DefaultAttributesExtensionTest extends TestCase
{
    /**
     * @dataProvider provideTestCases
     *
     * @param array<string, mixed> $config
     */
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
