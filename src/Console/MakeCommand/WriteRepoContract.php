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
    protected $signature = 'make:writerepocontract {domain} {name} {--force=0}';

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
            return 1;
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
        return is_dir('App\\Domain\\' . $this->argument('domain') . '\\' . config('repomodel.paths.write.contract_path'))
            ? 'App\\Domain\\' . $this->argument('domain') . '\\' . config('repomodel.paths.write.contract_namespace')
            : $rootNamespace . '\\Domain\\' . $this->argument('domain') . '\\' . config('repomodel.paths.write.contract_namespace');
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
