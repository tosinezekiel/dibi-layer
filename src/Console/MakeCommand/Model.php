<?php

namespace CircleLinkHealth\ReposModelsGenerator\Console\MakeCommand;

use Illuminate\Foundation\Console\ModelMakeCommand;
use Illuminate\Support\Str;

class Model extends ModelMakeCommand
{
    public function handle()
    {
        parent::handle();

        $path = $this->getPath($this->qualifyClass($this->getNameInput()));

        file_put_contents($path, Str::replace('Illuminate\Database\Eloquent\Model', 'CircleLinkHealth\ReposModelsGenerator\Models\Model', file_get_contents($path)));
    }
}
