<?php

namespace League\CommonMark\Tests\Unit\Node;

use PHPUnit\Framework\TestCase;

class NodeTest extends TestCase
{
    public function testInsertBeforeElementWhichDoesNotHaveAPreviousOne()
    {
        $root = new SimpleNode();
        $root->appendChild($targetNode = new SimpleNode());

        $newNode = new SimpleNode();
        $targetNode->insertBefore($newNode);

        $this->assertNull($newNode->previous());
        $this->assertSame($targetNode, $newNode->next());
        $this->assertSame($newNode, $targetNode->previous());
    }

    public function testInsertBeforeElementWhichAlreadyHasPrevious()
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

    public function testPrependChildToChildlessParent()
    {
        $root = new SimpleNode();

        $root->prependChild($newNode = new SimpleNode());

        $this->assertSame($newNode, $root->firstChild());
        $this->assertSame($root, $newNode->parent());
        $this->assertNull($newNode->previous());
    }

    public function testPrependChildToParentWhichAlreadyHasChildren()
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

    public function testDetachChildren()
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

    public function testReplaceChildren()
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
}
