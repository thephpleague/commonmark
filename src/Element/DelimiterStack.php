<?php

namespace ColinODell\CommonMark\Element;

class DelimiterStack
{
    /**
     * @var Delimiter|null
     */
    protected $top;

    public function getTop()
    {
        return $this->top;
    }

    public function push(Delimiter $newDelimiter)
    {
        $newDelimiter->setPrevious($this->top);

        if ($this->top !== null) {
            $this->top->setNext($newDelimiter);
        }

        $this->top = $newDelimiter;
    }

    /**
     * @param Delimiter|null $stackBottom
     *
     * @return Delimiter|null
     */
    public function findEarliest(Delimiter $stackBottom = null)
    {
        $delimiter = $this->top;
        while ($delimiter !== null && $delimiter->getPrevious() !== $stackBottom) {
            $delimiter = $delimiter->getPrevious();
        }

        return $delimiter;
    }

    /**
     * @param Delimiter $delimiter
     */
    public function removeDelimiter(Delimiter $delimiter)
    {
        if ($delimiter->getPrevious() !== null) {
            $delimiter->getPrevious()->setNext($delimiter->getNext());
        }

        if ($delimiter->getNext() === null) {
            // top of stack
            $this->top = $delimiter->getPrevious();
        } else {
            $delimiter->getNext()->setPrevious($delimiter->getPrevious());
        }
    }

    /**
     * @param Delimiter|null $stackBottom
     */
    public function removeAll(Delimiter $stackBottom = null)
    {
        while ($this->top && $this->top !== $stackBottom) {
            $this->removeDelimiter($this->top);
        }
    }

    /**
     * @param string $character
     */
    public function removeEarlierMatches($character)
    {
        $opener = $this->top;
        $closerAbove = null;
        while ($opener !== null) {
            if ($opener->getChar() === $character) {
                if ($closerAbove) {
                    $closerAbove->setPrevious($opener->getPrevious());
                } else {
                    $this->top = $opener->getPrevious();
                }
            } else {
                $closerAbove = $opener;
            }
            $opener = $opener->getPrevious();
        }
    }

    /**
     * @param string|string[] $characters
     *
     * @return Delimiter|null
     */
    public function searchByCharacter($characters)
    {
        if (!is_array($characters)) {
            $characters = array($characters);
        }

        $opener = $this->top;
        while ($opener !== null) {
            if (in_array($opener->getChar(), $characters)) {
                break;
            }
            $opener = $opener->getPrevious();
        }

        return $opener;
    }
}
