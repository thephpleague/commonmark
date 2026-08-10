<?php

declare(strict_types=1);

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

namespace League\CommonMark\Tests\Unit\Node;

use PHPUnit\Framework\TestCase;

final class NodeTest extends TestCase
{
    public function testInsertBeforeElementWhichDoesNotHaveAPreviousOne(): void
    {
        $root = new SimpleNode();
        $root->appendChild($targetNode = new SimpleNode());

        $newNode = new SimpleNode();
        $targetNode->insertBefore($newNode);

        $this->assertNull($newNode->previous());
        $this->assertSame($targetNode, $newNode->next());
        $this->assertSame($newNode, $targetNode->previous());
    }

    public function testInsertBeforeElementWhichAlreadyHasPrevious(): void
    {
        $root = new SimpleNode();
        $root->appendChild($firstNode = new SimpleNode());
        $root->appendChild($targetNode = new SimpleNode());

        $newNode = new SimpleNode();
        $targetNode->insertBefore($newNode);

        $this->assertSame($firstNode, $newNode->previous());
        $this->assertSame($targetNode, $newNode->next());
        $this->assertSame($newNode, $targetNode->previous());
    }

    public function testPrependChildToChildlessParent(): void
    {
        $root = new SimpleNode();

        $root->prependChild($newNode = new SimpleNode());

        $this->assertSame($newNode, $root->firstChild());
        $this->assertSame($root, $newNode->parent());
        $this->assertNull($newNode->previous());
    }

    public function testPrependChildToParentWhichAlreadyHasChildren(): void
    {
        $root = new SimpleNode();
        $root->prependChild($existingChild = new SimpleNode());

        $this->assertSame($existingChild, $root->firstChild());

        $newNode = new SimpleNode();
        $root->prependChild($newNode);

        $this->assertCount(2, $root->children());
        $this->assertSame($newNode, $root->firstChild());
        $this->assertSame($root, $newNode->parent());
        $this->assertNull($newNode->previous());
        $this->assertSame($existingChild, $newNode->next());
        $this->assertSame($newNode, $existingChild->previous());
    }

    public function testDetachChildren(): void
    {
        $root = new SimpleNode();
        $root->appendChild($child1 = new SimpleNode());
        $root->appendChild($child2 = new SimpleNode());

        $root->detachChildren();

        $this->assertCount(0, $root->children());
        $this->assertNull($root->firstChild());
        $this->assertNull($root->lastChild());

        $this->assertNull($child1->parent());
        $this->assertNull($child2->parent());

        $this->assertSame($child2, $child1->next());
        $this->assertSame($child1, $child2->previous());
    }

    public function testReplaceChildren(): void
    {
        $root = new SimpleNode();
        $root->appendChild($oldChild = new SimpleNode());

        $newChildren = [
            $newChild1 = new SimpleNode(),
            $newChild2 = new SimpleNode(),
        ];

        $root->replaceChildren($newChildren);

        $this->assertCount(2, $root->children());
        $this->assertSame($newChild1, $root->firstChild());
        $this->assertSame($newChild2, $root->lastChild());
        $this->assertSame($root, $newChild1->parent());
        $this->assertSame($root, $newChild2->parent());
        $this->assertNull($oldChild->parent());
    }

    public function testInsertAfterWithParent(): void
    {
        $root = new SimpleNode();
        $root->appendChild($child1 = new SimpleNode());
        $root->appendChild($child2 = new SimpleNode());

        $otherRoot = new SimpleNode();
        $otherRoot->appendChild($child3 = new SimpleNode());
        $otherRoot->appendChild($child4 = new SimpleNode());

        $child1->insertAfter($child3);

        $this->assertCount(3, $root->children());
        $this->assertCount(1, $otherRoot->children());
        $this->assertSame($child1, $root->firstChild());
        $this->assertSame($child2, $root->lastChild());
        $this->assertSame($root, $child2->parent());
    }

    public function testInsertAfterWithoutParent(): void
    {
        $node1 = new SimpleNode();
        $node2 = new SimpleNode();

        $node1->insertAfter($node2);

        $this->assertSame($node2, $node1->next());
        $this->assertSame($node1, $node2->previous());
    }

    public function testInsertBeforeWithParent(): void
    {
        $root = new SimpleNode();
        $root->appendChild($child1 = new SimpleNode());
        $root->appendChild($child2 = new SimpleNode());
        $root->appendChild($child3 = new SimpleNode());

        $otherRoot = new SimpleNode();
        $otherRoot->appendChild($child4 = new SimpleNode());
        $otherRoot->appendChild($child5 = new SimpleNode());

        $child2->insertBefore($child4);

        $this->assertCount(4, $root->children());
        $this->assertCount(1, $otherRoot->children());
        $this->assertSame($child1, $root->children()[0]);
        $this->assertSame($child4, $root->children()[1]);
        $this->assertSame($child2, $root->children()[2]);
        $this->assertSame($child3, $root->children()[3]);
        $this->assertSame($root, $child4->parent());
    }

    public function testInsertBeforeWithoutParent(): void
    {
        $node1 = new SimpleNode();
        $node2 = new SimpleNode();

        $node1->insertBefore($node2);

        $this->assertSame($node1, $node2->next());
        $this->assertSame($node2, $node1->previous());
    }

    public function testClone(): void
    {
        // Build our intial AST
        $root = new SimpleNode();
        $root->appendChild($child1 = new SimpleNode());
        $root->appendChild($child2 = new SimpleNode());
        $child1->appendChild($grandChild1 = new SimpleNode());
        $child1->appendChild($grandChild2 = new SimpleNode());
        $grandChild2->appendChild($greatGrandChild1 = new SimpleNode());

        // Set values on each node to indicate they are originals
        $walker = $root->walker();
        while ($event = $walker->next()) {
            $event->getNode()->value = 'original';
        }

        // Clone one of the children
        $cloneOfChild1 = clone $child1;
        // Set values throught the cloned node to indicate they are clones
        $walker = $cloneOfChild1->walker();
        while ($event = $walker->next()) {
            $event->getNode()->value = 'cloned';
        }

        // Now check the original to ensure nothing changed there
        $walker = $root->walker();
        while ($event = $walker->next()) {
            $this->assertSame('original', $event->getNode()->value);
        }
    }

    public function testCloneLeavesTheOriginalChildrenAttached(): void
    {
        $root = new SimpleNode();
        $root->appendChild($child = new SimpleNode());
        $child->appendChild($grandChild1 = new SimpleNode());
        $child->appendChild($grandChild2 = new SimpleNode());

        $copy = clone $child;

        // The original keeps its children, which still point back to it
        $this->assertSame($root, $child->parent());
        $this->assertSame($child, $grandChild1->parent());
        $this->assertSame($child, $grandChild2->parent());

        // The copy has its own children, which point back to the copy
        $copiedChildren = $copy->children();
        $this->assertNull($copy->parent());
        $this->assertCount(2, $copiedChildren);
        $this->assertNotSame($grandChild1, $copiedChildren[0]);
        $this->assertNotSame($grandChild2, $copiedChildren[1]);
        $this->assertSame($copy, $copiedChildren[0]->parent());
        $this->assertSame($copy, $copiedChildren[1]->parent());
    }

    public function testCloneGivesEveryNodeInTheSubtreeItsOwnData(): void
    {
        $node = new SimpleNode();
        $node->appendChild($child = new SimpleNode());
        $node->data->set('attributes/class', 'original');
        $child->data->set('attributes/class', 'original-child');

        $copy = clone $node;
        $copy->data->set('attributes/class', 'copy');
        $copy->firstChild()->data->set('attributes/class', 'copy-child');

        $this->assertSame('original', $node->data->get('attributes/class'));
        $this->assertSame('original-child', $child->data->get('attributes/class'));
        $this->assertSame('copy', $copy->data->get('attributes/class'));
        $this->assertSame('copy-child', $copy->firstChild()->data->get('attributes/class'));
    }
}
