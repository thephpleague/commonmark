<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (http://bitly.com/commonmarkjs)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Tests;

use League\CommonMark\Environment;

class EnvironmentTest extends \PHPUnit_Framework_TestCase
{
    public function testAddGetExtensions()
    {
        $extension = $this->getMockForAbstractClass('League\CommonMark\Extension\ExtensionInterface');
        $extension->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('foo'));

        $environment = new Environment();
        $this->assertCount(0, $environment->getExtensions());

        $environment->addExtension($extension);

        $extensions = $environment->getExtensions();
        $this->assertCount(1, $extensions);
        $this->assertEquals($extension, $extensions['foo']);
    }
} 