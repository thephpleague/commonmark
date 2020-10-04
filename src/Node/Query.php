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

namespace League\CommonMark\Node;

use League\CommonMark\Node\Query\AndExpr;
use League\CommonMark\Node\Query\OrExpr;

final class Query
{
    /** @var callable(Node): bool $condition */
    private $condition;

    public function __construct()
    {
        $this->condition = new AndExpr();
    }

    public function where(callable ...$conditions): self
    {
        return $this->andWhere(...$conditions);
    }

    public function andWhere(callable ...$conditions): self
    {
        if ($this->condition instanceof AndExpr) {
            foreach ($conditions as $condition) {
                $this->condition->add($condition);
            }
        } else {
            $this->condition = new AndExpr($this->condition, ...$conditions);
        }

        return $this;
    }

    public function orWhere(callable ...$conditions): self
    {
        if ($this->condition instanceof OrExpr) {
            foreach ($conditions as $condition) {
                $this->condition->add($condition);
            }
        } else {
            $this->condition = new OrExpr($this->condition, ...$conditions);
        }

        return $this;
    }

    public function findOne(Node $node): ?Node
    {
        $walker = $node->walker();
        while ($event = $walker->next()) {
            if (! $event->isEntering()) {
                continue;
            }

            if (\call_user_func($this->condition, $node = $event->getNode())) {
                return $node;
            }
        }

        return null;
    }

    /**
     * @return iterable<Node>
     */
    public function findAll(Node $node, ?int $limit = PHP_INT_MAX): iterable
    {
        /** @var Node[] $results */
        $results     = [];
        $resultCount = 0;

        $walker = $node->walker();
        while ($event = $walker->next()) {
            if (! $event->isEntering()) {
                continue;
            }

            if (\call_user_func($this->condition, $event->getNode())) {
                $results[] = $event->getNode();
                ++$resultCount;
            }

            if ($resultCount >= $limit) {
                break;
            }
        }

        return $results;
    }

    /**
     * @return callable(Node): bool
     */
    public static function type(string $class): callable
    {
        return static function (Node $node) use ($class): bool {
            return $node instanceof $class;
        };
    }

    /**
     * @param ?callable $condition
     *
     * @psalm-param ?callable(Node): bool $condition
     *
     * @return callable(Node): bool
     */
    public static function hasChild(?callable $condition = null): callable
    {
        return static function (Node $node) use ($condition): bool {
            foreach ($node->children() as $child) {
                if ($condition === null || $condition($child)) {
                    return true;
                }
            }

            return false;
        };
    }

    /**
     * @param ?callable $condition
     *
     * @psalm-param ?callable(Node): bool $condition
     *
     * @return callable(Node): bool
     */
    public static function hasParent(?callable $condition = null): callable
    {
        return static function (Node $node) use ($condition): bool {
            $parent = $node->parent();
            if ($parent === null) {
                return false;
            }

            if ($condition === null) {
                return true;
            }

            return $condition($parent);
        };
    }
}
