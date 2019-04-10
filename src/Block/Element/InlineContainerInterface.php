<?php

namespace League\CommonMark\Block\Element;

interface InlineContainerInterface extends StringContainerInterface
{
    public function getStringContent(): string;
}
