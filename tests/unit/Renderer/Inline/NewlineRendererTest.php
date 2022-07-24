<?php

declare(strict_types=1);

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (https://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Tests\Unit\Renderer\Inline;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Exception\InvalidArgumentException;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Node\Inline\AbstractInline;
use League\CommonMark\Node\Inline\Newline;
use League\CommonMark\Renderer\Inline\NewlineRenderer;
use League\CommonMark\Tests\Unit\Renderer\FakeChildNodeRenderer;
use League\Config\ConfigurationInterface;
use PHPUnit\Framework\TestCase;

final class NewlineRendererTest extends TestCase
{
    private NewlineRenderer $renderer;

    protected function setUp(): void
    {
        $this->renderer = new NewlineRenderer();
    }

    public function testRenderHardbreak(): void
    {
        $inline       = new Newline(Newline::HARDBREAK);
        $fakeRenderer = new FakeChildNodeRenderer();

        $result = $this->renderer->render($inline, $fakeRenderer);

        $this->assertIsString($result);
        $this->assertStringContainsString('<br />', $result);
    }

    public function testRenderSoftbreak(): void
    {
        $inline       = new Newline(Newline::SOFTBREAK);
        $fakeRenderer = new FakeChildNodeRenderer();
        $this->renderer->setConfiguration($this->createConfiguration(['renderer' => ['soft_break' => '::softbreakChar::']]));

        $result = $this->renderer->render($inline, $fakeRenderer);

        $this->assertIsString($result);
        $this->assertStringContainsString('::softbreakChar::', $result);
    }

    public function testRenderWithInvalidType(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $inline       = $this->getMockForAbstractClass(AbstractInline::class);
        $fakeRenderer = new FakeChildNodeRenderer();

        $this->renderer->render($inline, $fakeRenderer);
    }

    /**
     * @param array<string, mixed> $values
     */
    private function createConfiguration(array $values = []): ConfigurationInterface
    {
        $config = Environment::createDefaultConfiguration();
        (new CommonMarkCoreExtension())->configureSchema($config);
        $config->merge($values);

        return $config->reader();
    }
}
