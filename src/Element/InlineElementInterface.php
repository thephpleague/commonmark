<?php

namespace ColinODell\CommonMark\Element;

interface InlineElementInterface
{
    /**
     * @return $string
     */
    public function getType();

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setType($type);

    /**
     * @return mixed
     */
    public function getContents();

    /**
     * @param mixed $contents
     *
     * @return $this
     */
    public function setContents($contents);

    /**
     * @param string $attrName
     *
     * @return mixed
     */
    public function getAttribute($attrName);

    /**
     * @param string $attrName
     * @param mixed  $value
     *
     * @return $this
     */
    public function setAttribute($attrName, $value);
}
