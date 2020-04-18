<?php

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
use League\CommonMark\Parser\DocParser;
use PHPUnit\Framework\TestCase;

class DocParserTest extends TestCase
{
    /**
     * @expectedException \League\CommonMark\Exception\UnexpectedEncodingException
     */
    public function testParsingWithInvalidUTF8()
    {
        $environment = Environment::createCommonMarkEnvironment();
        $docParser = new DocParser($environment);

        $docParser->parse("\x09\xca\xca");
    }
}
