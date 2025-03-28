<?php

namespace Dibi\ReposModelsGenerator\Console\MakeCommand\Concerns;

use Illuminate\Support\Facades\Artisan;

trait CallsMakeCommands
{
    public function createModel(string $domain, string $name, bool $force = false)
    {
        $this->preRun($command = 'make:model', $name, $domain, $force);
        Artisan::call(
            $command,
            [
                'name'    => 'Domain/' . $domain . '/Models/' . $name,
                '--force' => $force,
            ]
        );
        $this->postRun($command = 'make:model', $name, $domain, $force);
    }

    public function createReadRepoContract(string $domain, string $name, bool $force = false)
    {
        $this->preRun($command = 'make:readrepocontract', $name, $domain, $force);
        Artisan::call(
            $command,
            [
                'name'    => $name,
                'domain'    => $domain,
                '--force' => $force,
            ]
        );
        $this->postRun($command = 'make:readrepocontract', $name, $domain, $force);
    }

    public function createReadRepos(string $domain, string $name, bool $force = false)
    {
        $this->preRun($command = 'make:readrepos', $name, $domain, $force);
        Artisan::call(
            $command,
            [
                'name'    => $name,
                'domain'    => $domain,
                '--force' => $force,
            ]
        );
        $this->postRun($command = 'make:readrepos', $name, $domain, $force);
    }

    public function createWriteRepoContract(string $domain, string $name, bool $force = false)
    {
        $this->preRun($command = 'make:writerepocontract', $name, $domain, $force);
        Artisan::call(
            $command,
            [
                'name'    => $name,
                'domain'    => $domain,
                '--force' => $force,
            ]
        );
        $this->postRun($command = 'make:writerepocontract', $name, $domain, $force);
    }

    public function createWriteRepos(string $domain, string $name, bool $force = false)
    {
        $this->preRun($command = 'make:writerepos', $name, $domain, $force);
        Artisan::call(
            $command,
            [
                'name'    => $name,
                'domain'    => $domain,
                '--force' => $force,
            ]
        );
        $this->postRun($command = 'make:writerepos', $name, $domain, $force);
    }

    public function generateServiceProvider()
    {
        $this->warn("Generating repository service provider...");
        Artisan::call('repo:provider');
        $output = Artisan::output();
        $this->info($output);
    }

    private function postRun(string $command, string $name, string $domain, bool $force)
    {
        $this->info("Finish `php artisan $command $name in $domain --force=$force`");
    }

    private function preRun(string $command, string $name, string $domain, bool $force)
    {
        $this->warn("Start `php artisan $command $name in $domain --force=$force`");
    }
}
