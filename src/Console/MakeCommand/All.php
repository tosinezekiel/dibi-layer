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
    protected $signature = 'make:repos {name} {--force=0}';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $force = (bool) $this->option('force');
        $name  = (string) $this->argument('name');

        $this->createModel($name, $force);
        $this->createReadRepoContract($name, $force);
        $this->createWriteRepoContract($name, $force);
        $this->createReadRepos($name, $force);
        $this->createWriteRepos($name, $force);
    }
}
