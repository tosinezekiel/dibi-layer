<?php

namespace Dibi\ReposModelsGenerator\Console\MakeCommand;

use Illuminate\Console\Command;
use Dibi\ReposModelsGenerator\Console\MakeCommand\Concerns\CallsMakeCommands;

class All extends Command
{
    use CallsMakeCommands;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Model, Interface and Read/Write Repositories';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'make:repos {domain} {name} {--force=0} {--register=1}';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $force = (bool) $this->option('force');
        $register = (bool) $this->option('register');
        $domain = (string) $this->argument('domain');
        $name = (string) $this->argument('name');

        $this->createModel($domain, $name, $force);
        $this->createReadRepoContract($domain, $name, $force);
        $this->createWriteRepoContract($domain, $name, $force);
        $this->createReadRepos($domain, $name, $force);
        $this->createWriteRepos($domain, $name, $force);

        if ($register) {
            $this->info("Generating repository service provider...");
            $this->call('repo:provider');
        }
    }
}
