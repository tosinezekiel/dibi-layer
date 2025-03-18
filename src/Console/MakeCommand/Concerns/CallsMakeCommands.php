<?php

namespace Dibi\ReposModelsGenerator\Console\MakeCommand\Concerns;

use Illuminate\Support\Facades\Artisan;

trait CallsMakeCommands
{
    public function createModel(string $name, bool $force = false)
    {
        $this->preRun($command = 'make:model', $name, $force);
        Artisan::call(
            $command,
            [
                'name'    => $name,
                '--force' => $force,
            ]
        );
        $this->postRun($command = 'make:model', $name, $force);
    }

    public function createReadRepoContract(string $name, bool $force = false)
    {
        $this->preRun($command = 'make:readrepocontract', $name, $force);
        Artisan::call(
            $command,
            [
                'name'    => $name,
                '--force' => $force,
            ]
        );
        $this->postRun($command = 'make:readrepocontract', $name, $force);
    }

    public function createReadRepos(string $name, bool $force = false)
    {
        $this->preRun($command = 'make:readrepos', $name, $force);
        Artisan::call(
            $command,
            [
                'name'    => $name,
                '--force' => $force,
            ]
        );
        $this->postRun($command = 'make:readrepos', $name, $force);
    }

    public function createWriteRepoContract(string $name, bool $force = false)
    {
        $this->preRun($command = 'make:writerepocontract', $name, $force);
        Artisan::call(
            $command,
            [
                'name'    => $name,
                '--force' => $force,
            ]
        );
        $this->postRun($command = 'make:writerepocontract', $name, $force);
    }

    public function createWriteRepos(string $name, bool $force = false)
    {
        $this->preRun($command = 'make:writerepos', $name, $force);
        Artisan::call(
            $command,
            [
                'name'    => $name,
                '--force' => $force,
            ]
        );
        $this->postRun($command = 'make:writerepos', $name, $force);
    }

    public function generateServiceProvider()
    {
        Artisan::call('repo:provider');
    }

    private function postRun(string $command, string $name, bool $force)
    {
        $this->info("Finish `php artisan $command $name --force=$force`");
    }

    private function preRun(string $command, string $name, bool $force)
    {
        $this->warn("Start `php artisan $command $name --force=$force`");
    }
}
