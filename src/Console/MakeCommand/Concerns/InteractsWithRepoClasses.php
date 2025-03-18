<?php

namespace CircleLinkHealth\ReposModelsGenerator\Console\MakeCommand\Concerns;

use CircleLinkHealth\ReposModelsGenerator\Contracts\ReadRepo;
use CircleLinkHealth\ReposModelsGenerator\Contracts\WriteRepo;

trait InteractsWithRepoClasses
{
    private function exists(string $class)
    {
        return class_exists($class) || interface_exists($class);
    }

    private function implementsReadRepo(string $class)
    {
        return $this->exists($class) && isset(class_implements($class)[ReadRepo::class]);
    }

    private function implementsWriteRepo(string $class)
    {
        return $this->exists($class) && isset(class_implements($class)[WriteRepo::class]);
    }

    private function isRepo(string $contract)
    {
        return $this->implementsWriteRepo($contract) || $this->implementsReadRepo($contract);
    }
}
