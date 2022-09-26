<?php

declare(strict_types=1);

namespace League\CommonMark\Extension\Marker;

use League\CommonMark\Node\Inline\AbstractInline;
use League\CommonMark\Node\Inline\DelimitedInterface;

final class Marker extends AbstractInline implements DelimitedInterface
{
    private string $delimiter;

    public function __construct(string $delimiter = '==')
    {
        parent::__construct();

        $this->delimiter = $delimiter;
    }

    public function getOpeningDelimiter(): string
    {
        return $this->delimiter;
    }

    public function getClosingDelimiter(): string
    {
        return $this->delimiter;
    }
}