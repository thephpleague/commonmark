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

use League\CommonMark\Extension\Footnote\Node\Footnote;
use League\CommonMark\Extension\Footnote\Renderer\FootnoteRenderer;
use League\CommonMark\Reference\Reference;
use League\CommonMark\Tests\Unit\FakeHtmlRenderer;
use League\CommonMark\Util\Configuration;
use PHPUnit\Framework\TestCase;

final class FootnoteRendererTest extends TestCase
{
    public function testDefaultAttributes(): void
    {
        $renderer = new FootnoteRenderer();
        $renderer->setConfiguration(new Configuration());

        $fakeReference = new Reference('label', 'dest', 'title');
        $footnote = new Footnote($fakeReference);

        $output = $renderer->render($footnote, new FakeHtmlRenderer(), false);

        $this->assertSame('footnote', $output->getAttribute('class'));
        $this->assertSame('fn:label', $output->getAttribute('id'));
        $this->assertSame('doc-endnote', $output->getAttribute('role'));
    }

    public function testCustomClassAddedViaAST(): void
    {
        $renderer = new FootnoteRenderer();
        $renderer->setConfiguration(new Configuration());

        $fakeReference = new Reference('label', 'dest', 'title');
        $footnote = new Footnote($fakeReference);
        $footnote->data['attributes']['class'] = 'custom class';

        $output = $renderer->render($footnote, new FakeHtmlRenderer(), false);

        $this->assertSame('custom class', $output->getAttribute('class'));
    }

    public function testClassConfiguration(): void
    {
        $renderer = new FootnoteRenderer();
        $renderer->setConfiguration(new Configuration(['footnote' => ['footnote_class' => 'my-custom-class']]));

        $fakeReference = new Reference('label', 'dest', 'title');
        $footnote = new Footnote($fakeReference);

        $output = $renderer->render($footnote, new FakeHtmlRenderer(), false);

        $this->assertSame('my-custom-class', $output->getAttribute('class'));
    }

    public function testIdPrefixConfiguration(): void
    {
        $renderer = new FootnoteRenderer();
        $renderer->setConfiguration(new Configuration(['footnote' => ['footnote_id_prefix' => 'custom-']]));

        $fakeReference = new Reference('label', 'dest', 'title');
        $footnote = new Footnote($fakeReference);

        $output = $renderer->render($footnote, new FakeHtmlRenderer(), false);

        $this->assertSame('custom-label', $output->getAttribute('id'));
    }
}
