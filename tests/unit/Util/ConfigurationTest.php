<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Tests\Unit\Util;

use League\CommonMark\Util\Configuration;
use PHPUnit\Framework\TestCase;

class ConfigurationTest extends TestCase
{
    public function testGet()
    {
        $data = [
            'foo' => 'bar',
            'a'   => [
                'b' => 'c',
            ],
        ];

        $config = new Configuration($data);

        // No arguments should return the whole thing
        $this->assertEquals($data, $config->get());

        // Test getting a single scalar element
        $this->assertEquals('bar', $config->get('foo'));

        // Test getting a single array element
        $this->assertEquals($data['a'], $config->get('a'));

        // Test getting an element by path
        $this->assertEquals('c', $config->get('a/b'));

        // Test getting a path that's one level too deep
        $this->assertNull($config->get('a/b/c'));

        // Test getting a path with no existing components
        $this->assertNull($config->get('x/y/z'));

        // Test getting a path with a default that isn't a string or null
        $this->assertSame(true, $config->get('x/y/z', true));

        // Test getting a non-existent element
        $this->assertNull($config->get('test'));

        // Test getting a non-existent element with a default value
        $this->assertEquals(42, $config->get('answer', 42));
    }

    public function testSet()
    {
        $data = [
            'foo' => 'bar',
            'a'   => [
                'b' => 'c',
            ],
        ];

        $config = new Configuration($data);

        // Creating a new scalar
        $config->set('lucky_number', 3);
        $this->assertEquals(3, $config->get('lucky_number'));

        // Replacing the scalar with a null
        $config->set('lucky_number', null);
        $this->assertNull($config->get('lucky_number'));

        // Simple replacement of a scalar value
        $config->set('foo', 'baz');
        $this->assertEquals('baz', $config->get('foo'));

        // Replacing a scalar with an array
        $config->set('foo', ['bar' => 'baz']);
        $this->assertEquals(['bar' => 'baz'], $config->get('foo'));
        $this->assertEquals('baz', $config->get('foo/bar'));

        // Replacing a nested scalar
        $config->set('a/b', 'd');
        $this->assertEquals(['b' => 'd'], $config->get('a'));
        $this->assertEquals('d', $config->get('a/b'));

        // Replacing a nested scalar with an array
        $config->set('a/b/c', 'd');
        $this->assertEquals(['c' => 'd'], $config->get('a/b'));
        $this->assertEquals('d', $config->get('a/b/c'));

        // Replacing a nested array with a scalar
        $config->set('a/b', 'c');
        $this->assertEquals(['b' => 'c'], $config->get('a'));
        $this->assertEquals('c', $config->get('a/b'));

        // Creating a brand new nested array
        $config->set('x/y/z', 42);
        $this->assertEquals(['y' => ['z' => 42]], $config->get('x'));
        $this->assertEquals(['z' => 42], $config->get('x/y'));
        $this->assertSame(42, $config->get('x/y/z'));
    }

    public function testReplace()
    {
        $config = new Configuration(['foo' => 'bar']);
        $config->replace(['test' => '123']);

        $this->assertNull($config->get('foo'));
        $this->assertEquals('123', $config->get('test'));
    }

    public function testMerge()
    {
        $config = new Configuration(['foo' => 'bar', 'test' => '123']);
        $config->merge(['test' => '456']);

        $this->assertEquals('bar', $config->get('foo'));
        $this->assertEquals('456', $config->get('test'));
    }
}
