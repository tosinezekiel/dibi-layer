<?php

namespace Dibi\ReposModelsGenerator\Console\MakeCommand;

class ReadRepos extends BaseMake
{
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create new read repo classes';
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:readrepos';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Read Repos';
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
            $this->addToPestDataset();
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
        return $this->getReadNamespaceForDriver($this->driver);
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->resolveStubPath('/stubs/readrepo.stub');
    }

    protected function replaceContract(string $stub)
    {
        $name     = $this->getNameInput();
        $contract = $this->getReadContractFQN($name);

        if ( ! interface_exists($contract)) {
            $this->warn("Creating [$contract] does not exist.");
            $this->createReadRepoContract($this->argument('domain'), $this->argument('name'), $this->option('force'));
            $this->line("Created [$contract] does not exist.");
        }

        $alias = $name.'Contract';

        return str_replace(['{{ contractNamespace }}', '{{contractNamespace}}'], "$contract as $alias", str_replace(['{{ contract }}', '{{contract}}'], $alias, $stub));
    }

    private function addToPestDataset()
    {
    }
}
