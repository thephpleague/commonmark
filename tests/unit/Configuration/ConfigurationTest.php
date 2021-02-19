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

namespace League\CommonMark\Tests\Unit\Configuration;

use League\CommonMark\Configuration\Configuration;
use League\CommonMark\Exception\InvalidConfigurationException;
use Nette\Schema\Expect;
use Nette\Schema\ValidationException;
use PHPUnit\Framework\TestCase;

final class ConfigurationTest extends TestCase
{
    public function testAddSchema(): void
    {
        $config = new Configuration();

        try {
            $config->get('foo');
            $this->fail('A validation exception should be thrown since no schemas exist yet');
        } catch (\Throwable $t) {
            $this->assertInstanceOf(InvalidConfigurationException::class, $t);
        }

        $config->addSchema('foo', Expect::string()->default('bar'));
        $this->assertSame('bar', $config->get('foo'));

        $config->addSchema('a', Expect::string()->required());

        try {
            $config->get('foo');
            $this->fail('A validation exception should be thrown since the full schema doesn\'t pass validation');
        } catch (\Throwable $t) {
            $this->assertInstanceOf(InvalidConfigurationException::class, $t);
            $this->assertInstanceOf(ValidationException::class, $t->getPrevious());
        }

        // Overwrite the previous schema we added
        $config->addSchema('a', Expect::int(42));

        $this->assertSame(42, $config->get('a'));
    }

    public function testGet(): void
    {
        $config = new Configuration([
            'foo' => Expect::string('bar'),
            'required_number' => Expect::int()->required(),
            'default_string' => Expect::string()->default('default'),
            'nested' => Expect::structure([
                'lucky_number' => Expect::int()->default(42),
            ])->castTo('array'),
            'array' => Expect::arrayOf('mixed'),
        ]);

        $config->merge([
            'required_number' => 3,
            'array' => ['a' => 1, 'b' => 2],
        ]);

        // Test getting a single scalar element
        $this->assertSame(3, $config->get('required_number'));

        // Test getting a single scalar element with a default value
        $this->assertSame('bar', $config->get('foo'));

        // Test getting a single array element
        $this->assertSame(['a' => 1, 'b' => 2], $config->get('array'));

        // Test getting a nested array element by path
        $this->assertSame(2, $config->get('array/b'));
        $this->assertSame(2, $config->get('array.b'));

        // Test getting a nested structure element by path
        $this->assertSame(42, $config->get('nested/lucky_number'));
        $this->assertSame(42, $config->get('nested.lucky_number'));
    }

    public function testGetNonExistentPath(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessageMatches('/does not exist/');

        (new Configuration())->get('does-not-exist');
    }

    public function testGetWhenSchemaValidationFails(): void
    {
        $config = new Configuration();
        $config->addSchema('foo', Expect::int()->required());
        $config->addSchema('bar', Expect::int());

        try {
            $config->get('foo');
            $this->fail('A validation exception should have been thrown');
        } catch (\Throwable $t) {
            $this->assertInstanceOf(InvalidConfigurationException::class, $t);
            $this->assertInstanceOf(ValidationException::class, $t->getPrevious());
        }

        try {
            $config->set('bar', 'not an integer value');
            $config->get('bar');
            $this->fail('A validation exception should have been thrown');
        } catch (\Throwable $t) {
            $this->assertInstanceOf(InvalidConfigurationException::class, $t);
            $this->assertInstanceOf(ValidationException::class, $t->getPrevious());
        }
    }

    public function testExists(): void
    {
        $config = new Configuration([
            'str' => Expect::string(''),
            'int' => Expect::int(0),
            'arr' => Expect::array([]),
            'null' => Expect::null(),
            'nested' => Expect::structure([
                'foo' => Expect::type('int')->nullable(),
            ])->castTo('array'),
        ]);

        $this->assertTrue($config->exists('str'));
        $this->assertTrue($config->exists('int'));
        $this->assertTrue($config->exists('arr'));
        $this->assertTrue($config->exists('null'));
        $this->assertTrue($config->exists('nested'));
        $this->assertTrue($config->exists('nested/foo'));

        $this->assertFalse($config->exists('does-not-exist'));
        $this->assertFalse($config->exists('arr.0'));
        $this->assertFalse($config->exists('nested/bar'));
        $this->assertFalse($config->exists('nested/foo/bar'));
    }

    public function testSet(): void
    {
        $config = new Configuration([
            'foo' => Expect::type('int|string'),
        ]);

        $config->set('foo', 'bar');
        $this->assertSame('bar', $config->get('foo'));

        $config->set('foo', 42);
        $this->assertSame(42, $config->get('foo'));

        $config->set('foo', new \DateTimeImmutable());
        $this->expectException(InvalidConfigurationException::class);
        $this->assertSame('bar', $config->get('foo'));
    }

    public function testSetNested(): void
    {
        $config = new Configuration([
            'a' => Expect::structure([
                'b' => Expect::structure([
                    'c' => Expect::string('d'),
                ])->castTo('array'),
            ])->castTo('array'),
        ]);

        $config->set('a/b/c', 'e');
        $this->assertSame('e', $config->get('a/b/c'));
        $this->assertSame(['c' => 'e'], $config->get('a/b'));
        $this->assertSame(['b' => ['c' => 'e']], $config->get('a'));

        $config->set('a/b', ['c' => 'f']);
        $this->assertSame('f', $config->get('a/b/c'));
        $this->assertSame(['c' => 'f'], $config->get('a/b'));
        $this->assertSame(['b' => ['c' => 'f']], $config->get('a'));

        $config->set('a', ['b' => ['c' => 'g']]);
        $this->assertSame('g', $config->get('a/b/c'));
        $this->assertSame(['c' => 'g'], $config->get('a/b'));
        $this->assertSame(['b' => ['c' => 'g']], $config->get('a'));
    }

    public function testSetInvalidType(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessageMatches("/item 'foo' expects to be int/");

        $config = new Configuration(['foo' => Expect::int()]);

        $config->set('foo', 'bar');
        $config->get('foo');
    }

    public function testSetUndefinedKey(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessageMatches("/Unexpected item 'bar'/");

        $config = new Configuration(['foo' => Expect::int(42)]);

        $config->set('bar', 3);
        $config->get('foo');
    }

    public function testMerge(): void
    {
        $config = new Configuration([
            'foo' => Expect::int(1),
            'bar' => Expect::int(1),
            'baz' => Expect::structure([
                'a' => Expect::int(1),
                'b' => Expect::int(1),
                'c' => Expect::int(1),
            ])->castTo('array'),
        ]);

        $config->set('foo', 1);
        $config->merge([
            'foo' => 2,
            'baz' => [
                'b' => 2,
            ],
        ]);

        $this->assertSame(2, $config->get('foo'));
        $this->assertSame(1, $config->get('bar'));
        $this->assertSame(1, $config->get('baz/a'));
        $this->assertSame(2, $config->get('baz/b'));
        $this->assertSame(1, $config->get('baz/c'));

        $config->merge([
            'foo' => 3,
            'baz' => [
                'c' => 3,
            ],
        ]);

        $this->assertSame(3, $config->get('foo'));
        $this->assertSame(1, $config->get('bar'));
        $this->assertSame(1, $config->get('baz/a'));
        $this->assertSame(2, $config->get('baz/b'));
        $this->assertSame(3, $config->get('baz/c'));
    }

    public function testReader(): void
    {
        $config = new Configuration(['foo' => Expect::string('bar')]);

        $reader = $config->reader();

        $this->assertSame('bar', $config->get('foo'));
        $this->assertSame('bar', $reader->get('foo'));

        $config->set('foo', 'baz');

        $this->assertSame('baz', $config->get('foo'));
        $this->assertSame('baz', $reader->get('foo'));
    }
}
