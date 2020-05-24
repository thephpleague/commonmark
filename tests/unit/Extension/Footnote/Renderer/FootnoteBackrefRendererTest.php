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

use League\CommonMark\Extension\Footnote\Node\FootnoteBackref;
use League\CommonMark\Extension\Footnote\Renderer\FootnoteBackrefRenderer;
use League\CommonMark\Reference\Reference;
use League\CommonMark\Tests\Unit\FakeHtmlRenderer;
use League\CommonMark\Util\Configuration;
use PHPUnit\Framework\TestCase;

final class FootnoteBackrefRendererTest extends TestCase
{
    public function testDefaultAttributes(): void
    {
        $renderer = new FootnoteBackrefRenderer();
        $renderer->setConfiguration(new Configuration());

        $fakeReference = new Reference('label', 'dest', 'title');
        $footnoteBackref = new FootnoteBackref($fakeReference);

        $output = $renderer->render($footnoteBackref, new FakeHtmlRenderer());

        $this->assertStringContainsString('class="footnote-backref"', $output);
        $this->assertStringContainsString('rev="footnote"', $output);
        $this->assertStringContainsString('role="doc-backlink"', $output);
    }

    public function testCustomClassAddedViaAST(): void
    {
        $renderer = new FootnoteBackrefRenderer();
        $renderer->setConfiguration(new Configuration());

        $fakeReference = new Reference('label', 'dest', 'title');
        $footnoteBackref = new FootnoteBackref($fakeReference);
        $footnoteBackref->data['attributes']['class'] = 'custom class';

        $output = $renderer->render($footnoteBackref, new FakeHtmlRenderer());

        $this->assertStringContainsString('class="custom class"', $output);
    }

    public function testClassConfiguration(): void
    {
        $renderer = new FootnoteBackrefRenderer();
        $renderer->setConfiguration(new Configuration(['footnote' => ['backref_class' => 'my-custom-class']]));

        $fakeReference = new Reference('label', 'dest', 'title');
        $footnoteBackref = new FootnoteBackref($fakeReference);

        $output = $renderer->render($footnoteBackref, new FakeHtmlRenderer());

        $this->assertStringContainsString('class="my-custom-class"', $output);
    }
}
