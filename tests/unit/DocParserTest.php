<?php

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

namespace League\CommonMark\Tests\Unit;

use League\CommonMark\DocParser;
use League\CommonMark\EnvironmentInterface;
use PHPUnit\Framework\TestCase;

class DocParserTest extends TestCase
{
    public function testGetEnvironment()
    {
        $environment = $this->getMockForAbstractClass(EnvironmentInterface::class);
        $docParser = new DocParser($environment);

        $this->assertSame($environment, $docParser->getEnvironment());
    }
}
