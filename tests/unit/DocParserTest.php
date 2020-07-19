<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Tests\Unit;

use League\CommonMark\DocParser;
use League\CommonMark\Environment;
use League\CommonMark\Exception\UnexpectedEncodingException;
use PHPUnit\Framework\TestCase;

class DocParserTest extends TestCase
{
    public function testParsingWithInvalidUTF8()
    {
        $this->expectException(UnexpectedEncodingException::class);
        $environment = Environment::createCommonMarkEnvironment();
        $docParser = new DocParser($environment);

        $docParser->parse("\x09\xca\xca");
    }
}
