<?php

namespace Dibi\ReposModelsGenerator\Console\MakeCommand;

class WriteRepoContract extends BaseMake
{
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new write repo contract class';
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:writerepocontract';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Write Repo Contract';

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

    /**
     * Get the default namespace for the class.
     *
     * @param  string $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return is_dir(config('repomodel.paths.write.contract_path')) ? config('repomodel.paths.write.contract_namespace') : $rootNamespace;
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->resolveStubPath('/stubs/writerepocontract.stub');
    }
}
