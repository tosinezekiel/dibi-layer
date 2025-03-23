<?php

namespace Dibi\ReposModelsGenerator\Console\MakeCommand;

class WriteRepos extends BaseMake
{
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new write repo classes';
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'make:writerepos {domain} {name} {--force=0}';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Write Repos';

    /**
     * The repo driver.
     *
     * @var string
     */
    private $driver;

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        foreach (config('repomodel.order') as $driver) {
            $this->driver = $driver;
            if (false === parent::handle() && ! $this->option('force')) {
                return false;
            }
        }
    }

    /**
     * Build the class with the given name.
     *
     * @param  string                                                 $name
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @return string
     */
    protected function buildClass($name)
    {
        $stub = $this->files->get($this->getStub());

        return $this->replaceModel(
            $this->replaceContract(
                $this->replaceNamespace($stub, $name)
                    ->replaceClass($stub, $name)
            )
        );
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $this->getWriteNamespaceForDriver($this->driver);
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->resolveStubPath('/stubs/writerepo.stub');
    }

    protected function replaceContract($stub)
    {
        $name     = $this->getNameInput();
        $contract = 'App\\Domain\\' . $this->argument('domain') . '\\' . config('repomodel.paths.write.contract_namespace').'\\'.$name;

        if ( ! interface_exists($contract)) {
            $this->warn("Creating [$contract]");
            $this->createWriteRepoContract($this->argument('domain'), $this->argument('name'), $this->option('force'));
            $this->line("Created [$contract]");
        }

        $alias = $name.'Contract';

        return str_replace(
            ['{{ contractNamespace }}', '{{contractNamespace}}'],
            "$contract as $alias",
            str_replace(['{{ contract }}', '{{contract}}'], $alias, $stub)
        );
    }
}
