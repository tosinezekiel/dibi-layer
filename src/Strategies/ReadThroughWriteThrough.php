<?php

namespace CircleLinkHealth\ReposModelsGenerator\Strategies;

class ReadThroughWriteThrough
{
    public function __construct(private array $implementationsInOrder)
    {
    }
}
