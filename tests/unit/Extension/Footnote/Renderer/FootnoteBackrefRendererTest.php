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

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\Footnote\FootnoteExtension;
use League\CommonMark\Extension\Footnote\Node\FootnoteBackref;
use League\CommonMark\Extension\Footnote\Renderer\FootnoteBackrefRenderer;
use League\CommonMark\Reference\Reference;
use League\CommonMark\Tests\Unit\Renderer\FakeChildNodeRenderer;
use League\Config\ConfigurationInterface;
use PHPUnit\Framework\TestCase;

final class FootnoteBackrefRendererTest extends TestCase
{
    public function testDefaultAttributes(): void
    {
        $renderer = new FootnoteBackrefRenderer();
        $renderer->setConfiguration($this->createConfiguration());

        $fakeReference   = new Reference('label', 'dest', 'title');
        $footnoteBackref = new FootnoteBackref($fakeReference);

        $output = $renderer->render($footnoteBackref, new FakeChildNodeRenderer());

        $this->assertStringContainsString('class="footnote-backref"', $output);
        $this->assertStringContainsString('rev="footnote"', $output);
        $this->assertStringContainsString('role="doc-backlink"', $output);
    }

    public function testCustomClassAddedViaAST(): void
    {
        $renderer = new FootnoteBackrefRenderer();
        $renderer->setConfiguration($this->createConfiguration());

        $fakeReference   = new Reference('label', 'dest', 'title');
        $footnoteBackref = new FootnoteBackref($fakeReference);

        $footnoteBackref->data->set('attributes/class', 'custom class');

        $output = $renderer->render($footnoteBackref, new FakeChildNodeRenderer());

        $this->assertStringContainsString('class="custom class footnote-backref"', $output);
    }

    public function testClassConfiguration(): void
    {
        $renderer = new FootnoteBackrefRenderer();
        $renderer->setConfiguration($this->createConfiguration(['footnote' => ['backref_class' => 'my-custom-class']]));

        $fakeReference   = new Reference('label', 'dest', 'title');
        $footnoteBackref = new FootnoteBackref($fakeReference);

        $output = $renderer->render($footnoteBackref, new FakeChildNodeRenderer());

        $this->assertStringContainsString('class="my-custom-class"', $output);
    }

    /**
     * @param array<string, mixed> $values
     */
    private function createConfiguration(array $values = []): ConfigurationInterface
    {
        $config = Environment::createDefaultConfiguration();
        (new FootnoteExtension())->configureSchema($config);
        $config->merge($values);

        return $config->reader();
    }
}
