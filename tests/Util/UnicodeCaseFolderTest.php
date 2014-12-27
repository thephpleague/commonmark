<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Tests\Util;

use League\CommonMark\Util\UnicodeCaseFolder;

class UnicodeCaseFolderTest extends \PHPUnit_Framework_TestCase
{
    public function testToUpperCase()
    {
        $this->assertEquals('FOO', UnicodeCaseFolder::toUpperCase('foo'));
        $this->assertEquals('ΑΓΩ', UnicodeCaseFolder::toUpperCase('αγω'));
        $this->assertEquals('ТОЛПОЙ', UnicodeCaseFolder::toUpperCase('толпой'));
        $this->assertEquals('ТОЛПОЙ', UnicodeCaseFolder::toUpperCase('Толпой'));
        $this->assertEquals('ТОЛПОЙ', UnicodeCaseFolder::toUpperCase('ТОЛПОЙ'));
    }
}
