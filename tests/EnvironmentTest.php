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

    public function testConstructor()
    {
        $config = array('foo' => 'bar');
        $environment = new Environment($config);
        $this->assertEquals('bar', $environment->getConfig('foo'));
    }

    public function testGetConfig()
    {
        $config = array(
            'foo' => 'bar',
            'a' => array(
                'b' => 'c'
            )
        );
        $environment = new Environment($config);

        // No arguments should return the whole thing
        $this->assertEquals($config, $environment->getConfig());

        // Test getting a single element
        $this->assertEquals('bar', $environment->getConfig('foo'));

        // Test getting an element by path
        $this->assertEquals('c', $environment->getConfig('a/b'));

        // Test getting a non-existent element
        $this->assertNull($environment->getConfig('test'));

        // Test getting a non-existent element with a default value
        $this->assertEquals(42, $environment->getConfig('answer', 42));
    }

    public function testSetConfig()
    {
        $environment = new Environment(array('foo' => 'bar'));
        $environment->setConfig(array('test' => '123'));
        $this->assertNull($environment->getConfig('foo'));
        $this->assertEquals('123', $environment->getConfig('test'));
    }

    public function testSetConfigAfterInit()
    {
        $this->setExpectedException('RuntimeException');

        $environment = new Environment();
        // This triggers the initialization
        $environment->getBlockParsers();
        $environment->setConfig(array('foo' => 'bar'));
    }

    public function testMergeConfig()
    {
        $environment = new Environment(array('foo' => 'bar', 'test' => '123'));
        $environment->mergeConfig(array('test' => '456'));
        $this->assertEquals('bar', $environment->getConfig('foo'));
        $this->assertEquals('456', $environment->getConfig('test'));
    }

    public function testMergeConfigAfterInit()
    {
        $this->setExpectedException('RuntimeException');

        $environment = new Environment();
        // This triggers the initialization
        $environment->getBlockParsers();
        $environment->mergeConfig(array('foo' => 'bar'));
    }
} 