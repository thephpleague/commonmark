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
use League\CommonMark\Extension\Footnote\Node\FootnoteRef;
use League\CommonMark\Extension\Footnote\Renderer\FootnoteRefRenderer;
use League\CommonMark\Reference\Reference;
use League\CommonMark\Tests\Unit\Renderer\FakeChildNodeRenderer;
use League\Config\ConfigurationInterface;
use PHPUnit\Framework\TestCase;

final class FootnoteRefRendererTest extends TestCase
{
    public function testDefaultAttributes(): void
    {
        $renderer = new FootnoteRefRenderer();
        $renderer->setConfiguration($this->createConfiguration());

        $fakeReference = new Reference('label', 'dest', 'title');
        $footnoteRef   = new FootnoteRef($fakeReference);

        $output = (string) $renderer->render($footnoteRef, new FakeChildNodeRenderer());

        $this->assertStringContainsString('id="fnref:label"', $output);
        $this->assertStringContainsString('class="footnote-ref"', $output);
        $this->assertStringContainsString('role="doc-noteref"', $output);
    }

    public function testCustomClassAddedViaAST(): void
    {
        $renderer = new FootnoteRefRenderer();
        $renderer->setConfiguration($this->createConfiguration());

        $fakeReference = new Reference('label', 'dest', 'title');
        $footnoteRef   = new FootnoteRef($fakeReference);

        $footnoteRef->data->set('attributes/class', 'custom class');

        $output = (string) $renderer->render($footnoteRef, new FakeChildNodeRenderer());

        $this->assertStringContainsString('class="custom class footnote-ref"', $output);
    }

    public function testClassConfiguration(): void
    {
        $renderer = new FootnoteRefRenderer();
        $renderer->setConfiguration($this->createConfiguration(['footnote' => ['ref_class' => 'my-custom-class']]));

        $fakeReference = new Reference('label', 'dest', 'title');
        $footnoteRef   = new FootnoteRef($fakeReference);

        $output = (string) $renderer->render($footnoteRef, new FakeChildNodeRenderer());

        $this->assertStringContainsString('class="my-custom-class"', $output);
    }

    public function testIdPrefixConfiguration(): void
    {
        $renderer = new FootnoteRefRenderer();
        $renderer->setConfiguration($this->createConfiguration(['footnote' => ['ref_id_prefix' => 'custom-']]));

        $fakeReference = new Reference('label', 'dest', 'title');
        $footnoteRef   = new FootnoteRef($fakeReference);

        $output = (string) $renderer->render($footnoteRef, new FakeChildNodeRenderer());

        $this->assertStringContainsString('id="custom-label"', $output);
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
