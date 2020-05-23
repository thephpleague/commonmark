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

namespace League\CommonMark\Tests\Unit\Extension\Footnote\Renderer;

use League\CommonMark\Extension\Footnote\Node\FootnoteRef;
use League\CommonMark\Extension\Footnote\Renderer\FootnoteRefRenderer;
use League\CommonMark\Reference\Reference;
use League\CommonMark\Tests\Unit\FakeHtmlRenderer;
use League\CommonMark\Util\Configuration;
use PHPUnit\Framework\TestCase;

final class FootnoteRefRendererTest extends TestCase
{
    public function testDefaultAttributes(): void
    {
        $renderer = new FootnoteRefRenderer();
        $renderer->setConfiguration(new Configuration());

        $fakeReference = new Reference('label', 'dest', 'title');
        $footnoteRef = new FootnoteRef($fakeReference);

        $output = (string) $renderer->render($footnoteRef, new FakeHtmlRenderer());

        $this->assertStringContainsString('id="fnref:label"', $output);
        $this->assertStringContainsString('class="footnote-ref"', $output);
        $this->assertStringContainsString('role="doc-noteref"', $output);
    }

    public function testCustomClassAddedViaAST(): void
    {
        $renderer = new FootnoteRefRenderer();
        $renderer->setConfiguration(new Configuration());

        $fakeReference = new Reference('label', 'dest', 'title');
        $footnoteRef = new FootnoteRef($fakeReference);
        $footnoteRef->data['attributes']['class'] = 'custom class';

        $output = (string) $renderer->render($footnoteRef, new FakeHtmlRenderer());

        $this->assertStringContainsString('class="custom class"', $output);
    }

    public function testClassConfiguration(): void
    {
        $renderer = new FootnoteRefRenderer();
        $renderer->setConfiguration(new Configuration(['footnote' => ['ref_class' => 'my-custom-class']]));

        $fakeReference = new Reference('label', 'dest', 'title');
        $footnoteRef = new FootnoteRef($fakeReference);

        $output = (string) $renderer->render($footnoteRef, new FakeHtmlRenderer());

        $this->assertStringContainsString('class="my-custom-class"', $output);
    }

    public function testIdPrefixConfiguration(): void
    {
        $renderer = new FootnoteRefRenderer();
        $renderer->setConfiguration(new Configuration(['footnote' => ['ref_id_prefix' => 'custom-']]));

        $fakeReference = new Reference('label', 'dest', 'title');
        $footnoteRef = new FootnoteRef($fakeReference);

        $output = (string) $renderer->render($footnoteRef, new FakeHtmlRenderer());

        $this->assertStringContainsString('id="custom-label"', $output);
    }
}
