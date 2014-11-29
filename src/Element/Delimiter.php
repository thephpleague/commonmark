<?php

namespace ColinODell\CommonMark\Element;

class Delimiter
{
    /** @var String */
    protected $char;

    /** @var int */
    protected $numDelims;

    /** @var int */
    protected $pos;

    /** @var Delimiter|null */
    protected $previous;

    /** @var Delimiter|null */
    protected $next;

    /** @var bool */
    protected $canOpen;

    /** @var bool */
    protected $canClose;

    /** @var int|null */
    protected $index;

    /**
     * @return boolean
     */
    public function canClose()
    {
        return $this->canClose;
    }

    /**
     * @param boolean $canClose
     *
     * @return $this
     */
    public function setCanClose($canClose)
    {
        $this->canClose = $canClose;

        return $this;
    }

    /**
     * @return boolean
     */
    public function canOpen()
    {
        return $this->canOpen;
    }

    /**
     * @param boolean $canOpen
     *
     * @return $this
     */
    public function setCanOpen($canOpen)
    {
        $this->canOpen = $canOpen;

        return $this;
    }

    /**
     * @return String
     */
    public function getChar()
    {
        return $this->char;
    }

    /**
     * @param String $char
     *
     * @return $this
     */
    public function setChar($char)
    {
        $this->char = $char;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * @param int|null $index
     *
     * @return $this
     */
    public function setIndex($index)
    {
        $this->index = $index;

        return $this;
    }

    /**
     * @return Delimiter|null
     */
    public function getNext()
    {
        return $this->next;
    }

    /**
     * @param Delimiter|null $next
     *
     * @return $this
     */
    public function setNext($next)
    {
        $this->next = $next;

        return $this;
    }

    /**
     * @return int
     */
    public function getNumDelims()
    {
        return $this->numDelims;
    }

    /**
     * @param int $numDelims
     *
     * @return $this
     */
    public function setNumDelims($numDelims)
    {
        $this->numDelims = $numDelims;

        return $this;
    }

    /**
     * @return int
     */
    public function getPos()
    {
        return $this->pos;
    }

    /**
     * @param int $pos
     *
     * @return $this
     */
    public function setPos($pos)
    {
        $this->pos = $pos;

        return $this;
    }

    /**
     * @return Delimiter|null
     */
    public function getPrevious()
    {
        return $this->previous;
    }

    /**
     * @param Delimiter|null $previous
     *
     * @return $this
     */
    public function setPrevious($previous)
    {
        $this->previous = $previous;

        return $this;
    }

    /**
     * @param string    $char
     * @param int       $numDelims
     * @param int       $pos
     * @param bool      $canOpen
     * @param bool      $canClose
     * @param Delimiter $current
     * @param int|null  $index
     *
     * @return Delimiter
     */
    public static function createNext($char, $numDelims, $pos, $canOpen, $canClose, Delimiter $current = null, $index = null)
    {
        $newDelimiter = new Delimiter();
        $newDelimiter->char = $char;
        $newDelimiter->numDelims = $numDelims;
        $newDelimiter->pos = $pos;
        $newDelimiter->previous = $current;
        $newDelimiter->canOpen = $canOpen;
        $newDelimiter->canClose = $canClose;
        $newDelimiter->index = $index;

        if ($current !== null) {
            $current->next = $newDelimiter;
        }

        return $newDelimiter;
    }
}
