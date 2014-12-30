<?php
namespace League\CommonMark;

interface EnvironmentInterface {
    /**
     * @return array
     */
    public function getBlockParsers();

    /**
     * @return array
     */
    public function getInlineParsers();

    /**
     * @return array
     */
    public function getInlineProcessors();

    /**
     * @return array
     */
    public function getBlockRenderers();

    /**
     * @return array
     */
    public function getInlineRenderers();
}
