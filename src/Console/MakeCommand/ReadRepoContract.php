<?php

namespace Dibi\ReposModelsGenerator\Console\MakeCommand;

class ReadRepoContract extends BaseMake
{
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new read repo contract class';
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:readrepocontract';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Read Repo Contract';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if (false === parent::handle() && ! $this->option('force')) {
            return false;
        }
    }

    protected function buildClass($name)
    {
        $stub = $this->files->get($this->getStub());

        return $this->replaceModel(
            $this->replaceNamespace($stub, $name)
                ->replaceClass($stub, $name)
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
        return is_dir(config('repomodel.paths.read.contract_path'))
        ? config('repomodel.paths.read.contract_namespace')
        : $rootNamespace;
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->resolveStubPath('/stubs/readrepocontract.stub');
    }
}
