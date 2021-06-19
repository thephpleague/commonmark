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

namespace League\CommonMark\Tests\Unit\Parser;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Exception\UnexpectedEncodingException;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Parser\MarkdownParser;
use PHPUnit\Framework\TestCase;

final class MarkdownParserTest extends TestCase
{
    public function testParsingWithInvalidUTF8(): void
    {
        $this->expectException(UnexpectedEncodingException::class);

        $environment = new Environment();
        $environment->addExtension(new CommonMarkCoreExtension());
        $docParser = new MarkdownParser($environment);

        $docParser->parse("\x09\xca\xca");
    }
}
