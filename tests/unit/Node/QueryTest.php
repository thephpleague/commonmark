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

namespace League\CommonMark\Tests\Unit\Node;

use League\CommonMark\Node\Node;
use League\CommonMark\Node\Query;
use PHPUnit\Framework\TestCase;

final class QueryTest extends TestCase
{
    public function testFindOne(): void
    {
        $result = (new Query())
            ->where(static function (Node $node): bool {
                return $node->data->get('number') === 7;
            })
            ->findOne($this->createAST());

        $this->assertNotNull($result);
        $this->assertSame(7, $result->data->get('number'));
    }

    public function testFindOneWhenNothingMatches(): void
    {
        $result = (new Query())
            ->where(Query::hasParent())
            ->findOne(new SimpleNode());

        $this->assertNull($result);
    }

    public function testFindOneWithNoCriteria(): void
    {
        $result = (new Query())
            ->findOne($this->createAST());

        $this->assertNotNull($result);
        $this->assertSame(1, $result->data->get('number'));
    }

    public function testFindAll(): void
    {
        $result = (new Query())
            ->where(Query::hasParent())
            ->findAll($this->createAST());

        $result = \iterator_to_array($result);

        $this->assertNodeCount(6, $result);

        // The order is based on node walking
        $this->assertSame(2, $result[0]->data->get('number'));
        $this->assertSame(3, $result[1]->data->get('number'));
        $this->assertSame(6, $result[2]->data->get('number'));
        $this->assertSame(4, $result[3]->data->get('number'));
        $this->assertSame(7, $result[4]->data->get('number'));
        $this->assertSame(5, $result[5]->data->get('number'));
    }

    public function testFindAllWithLimit(): void
    {
        $result = (new Query())
            ->where(Query::hasParent())
            ->findAll($this->createAST(), 3);

        $result = \iterator_to_array($result);

        $this->assertNodeCount(3, $result);

        $this->assertSame(2, $result[0]->data->get('number'));
        $this->assertSame(3, $result[1]->data->get('number'));
        $this->assertSame(6, $result[2]->data->get('number'));
    }

    public function testFindAllWithMixedConditions(): void
    {
        $ast = $this->createAST();

        // "class is SimpleNode AND has parent"
        $query = (new Query())->where(Query::type(SimpleNode::class), Query::hasParent());

        $this->assertNodeCount(6, $query->findAll($ast));

        // "class is SimpleNode AND has parent AND number > 2"
        $query->andWhere(static function (Node $node): bool {
            return $node->data->get('number') > 2;
        });

        $this->assertNodeCount(5, $query->findAll($ast));

        // "class is SimpleNode AND has parent AND number > 2 AND number > 3 AND number > 7"
        $query->andWhere(
            static function (Node $node): bool {
                return $node->data->get('number') > 3;
            },
            static function (Node $node): bool {
                return $node->data->get('number') < 7;
            }
        );

        $this->assertNodeCount(3, $query->findAll($ast));

        // "has child OR (class is SimpleNode AND has parent AND number > 2 AND number > 3 AND number > 7)"
        $query->orWhere(Query::hasChild());

        $this->assertNodeCount(5, $query->findAll($ast));

        // "has child OR (class is SimpleNode AND has parent AND number > 2 AND number > 3 AND number > 7) OR has parent OR class is SimpleNode"
        $query->orWhere(Query::hasParent(), Query::type(SimpleNode::class));

        $this->assertNodeCount(7, $query->findAll($ast));

        // "number > 4 AND (has child OR (class is SimpleNode AND has parent AND number > 2 AND number > 3 AND number > 7) OR has parent OR class is SimpleNode)"
        $query->andWhere(static function (Node $node): bool {
            return $node->data->get('number') > 4;
        });

        $this->assertNodeCount(3, $query->findAll($ast));
    }

    public function testWhereUsingExpressionInterface(): void
    {
        $query = (new Query())->where(new class implements Query\ExpressionInterface {
            public function __invoke(Node $node): bool
            {
                return $node->data->get('number') % 2 === 0;
            }
        });

        $this->assertNodeCount(3, $query->findAll($this->createAST()));
    }

    public function testWhereClausesUsingExpressionInterface(): void
    {
        $hasEvenNumber = new class implements Query\ExpressionInterface {
            public function __invoke(Node $node): bool
            {
                return $node->data->get('number') % 2 === 0;
            }
        };

        $greaterThanThree = new class implements Query\ExpressionInterface {
            public function __invoke(Node $node): bool
            {
                return $node->data->get('number') > 3;
            }
        };

        $isNumberFive = new class implements Query\ExpressionInterface {
            public function __invoke(Node $node): bool
            {
                return $node->data->get('number') === 5;
            }
        };

        $result = (new Query())
            ->where($hasEvenNumber)
            ->orWhere($isNumberFive)
            ->andWhere($greaterThanThree)
            ->findAll($this->createAST());

        $result = \iterator_to_array($result);

        $this->assertNodeCount(3, $result);
        $this->assertSame(6, $result[0]->data->get('number'));
        $this->assertSame(4, $result[1]->data->get('number'));
        $this->assertSame(5, $result[2]->data->get('number'));
    }

    public function testOrWhereUsingExpressionInterface(): void
    {
        $query = (new Query())
            ->where(Query::hasChild())
            ->orWhere(
                new class implements Query\ExpressionInterface {
                    public function __invoke(Node $node): bool
                    {
                        return $node->data->get('number') % 2 === 0;
                    }
                }
            );

        $this->assertNodeCount(5, $query->findAll($this->createAST()));
    }

    public function testFindAllWhenNothingMatches(): void
    {
        $result = (new Query())
            ->where(static function (Node $node): bool {
                return false;
            })
            ->findAll($this->createAST());

        $this->assertNodeCount(0, $result);
    }

    public function testFindAllWithNoCriteria(): void
    {
        $result = (new Query())->findAll($this->createAST());

        $this->assertNodeCount(7, $result);
    }

    public function testType(): void
    {
        $test = Query::type(SimpleNode::class);

        $this->assertTrue($test(new SimpleNode()));
        $this->assertFalse($test($this->createMock(Node::class)));
    }

    public function testHasChild(): void
    {
        $test = Query::hasChild();

        $parent = new SimpleNode();
        $child  = new SimpleNode();
        $parent->appendChild($child);

        $this->assertTrue($test($parent));
        $this->assertFalse($test($child));
    }

    public function testHasChildWithCondition(): void
    {
        $test = Query::hasChild(static function (Node $node): bool {
            return $node->data->has('test');
        });

        $parent = new SimpleNode();
        $child  = new SimpleNode();
        $parent->appendChild($child);

        $this->assertFalse($test($parent));
        $this->assertFalse($test($child));

        $child->data->set('test', true);
        $this->assertTrue($test($parent));
        $this->assertFalse($test($child));
    }

    public function testHasParent(): void
    {
        $test = Query::hasParent();

        $parent = new SimpleNode();
        $child  = new SimpleNode();
        $parent->appendChild($child);

        $this->assertFalse($test($parent));
        $this->assertTrue($test($child));
    }

    public function testHasParentWithCondition(): void
    {
        $test = Query::hasParent(static function (Node $node): bool {
            return $node->data->has('test');
        });

        $parent = new SimpleNode();
        $child  = new SimpleNode();
        $parent->appendChild($child);

        $this->assertFalse($test($parent));
        $this->assertFalse($test($child));

        $parent->data->set('test', true);

        $this->assertFalse($test($parent));
        $this->assertTrue($test($child));
    }

    private function createAST(): Node
    {
        $parent = new SimpleNode();
        $parent->data->set('number', 1);

        $child1 = new SimpleNode();
        $child1->data->set('number', 2);
        $parent->appendChild($child1);

        $child2 = new SimpleNode();
        $child2->data->set('number', 3);
        $parent->appendChild($child2);

        $child3 = new SimpleNode();
        $child3->data->set('number', 4);
        $parent->appendChild($child3);

        $child4 = new SimpleNode();
        $child4->data->set('number', 5);
        $parent->appendChild($child4);

        $grandchild1 = new SimpleNode();
        $grandchild1->data->set('number', 6);
        $child2->appendChild($grandchild1);

        $grandchild2 = new SimpleNode();
        $grandchild2->data->set('number', 7);
        $child3->appendChild($grandchild2);

        return $parent;
    }

    /**
     * @param iterable<mixed> $astIterator
     */
    private function assertNodeCount(int $expectedCount, iterable $astIterator): void
    {
        if (\is_array($astIterator) || $astIterator instanceof \Countable) {
            $this->assertCount($expectedCount, $astIterator);
        } else {
            $this->assertCount($expectedCount, \iterator_to_array($astIterator));
        }
    }
}
