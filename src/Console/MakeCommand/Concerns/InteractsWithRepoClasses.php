<?php

namespace Dibi\ReposModelsGenerator\Console\MakeCommand\Concerns;

use Dibi\ReposModelsGenerator\Contracts\ReadRepo;
use Dibi\ReposModelsGenerator\Contracts\WriteRepo;

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
