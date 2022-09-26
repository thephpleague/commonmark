<?php

declare(strict_types=1);

namespace League\CommonMark\Tests\Functional\Extension\Marker;

use League\CommonMark\ConverterInterface;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\Marker\MarkerExtension;
use League\CommonMark\Tests\Functional\AbstractLocalDataTest;
use League\CommonMark\Xml\MarkdownToXmlConverter;

final class MarkerXmlTest extends AbstractLocalDataTest
{
    protected function createConverter(array $config = []): ConverterInterface
    {
        $environment = new Environment($config);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new MarkerExtension());

        return new MarkdownToXmlConverter($environment);
    }

    public function dataProvider(): iterable
    {
        yield from $this->loadTests(__DIR__.'/xml', '*', '.md', '.xml');
    }
}
