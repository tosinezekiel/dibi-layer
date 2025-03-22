<?php

namespace Dibi\ReposModelsGenerator\Console\MakeCommand;

use Illuminate\Console\Concerns\CreatesMatchingTest;
use Illuminate\Console\GeneratorCommand;
use Dibi\ReposModelsGenerator\Console\MakeCommand\Concerns\CallsMakeCommands;
use Symfony\Component\Console\Input\InputOption;

abstract class BaseMake extends GeneratorCommand
{
    use CallsMakeCommands;
    use CreatesMatchingTest;

    public static function filenameSuffix(): string
    {
        return '';
    }

    protected function getAbstractReadRepoFQN(string $name)
    {
        return config('repomodel.paths.read.namespace').'\\'.$name;
    }

    protected function getAbstractWriteRepoFQN(string $name)
    {
        return config('repomodel.paths.write.namespace').'\\'.$name;
    }

    /**
     * Get the desired class name from the input.
     *
     * @return string
     */
    protected function getNameInput()
    {
        return trim($this->argument('name')).$this->filenameSuffix();
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['force', null, InputOption::VALUE_NONE, 'Create the class even if the model already exists'],
        ];
    }

    protected function getReadContractFQN(string $name)
    {
        return 'App\\Domain\\' . $this->argument('domain') . '\\' . config('repomodel.paths.read.contract_namespace').'\\'.$name;
    }

    protected function getReadNamespaceForDriver(string $driver)
    {
        return 'App\\Domain\\' . $this->argument('domain') . '\\' . config('repomodel.paths.read.namespace').'\\'.config("repomodel.drivers.$driver.dir");
    }

    protected function getWriteContractFQN(string $name)
    {
        return 'App\\Domain\\' . $this->argument('domain') . '\\' . config('repomodel.paths.write.contract_namespace').'\\'.$name;
    }

    protected function getWriteNamespaceForDriver(string $driver)
    {
        return 'App\\Domain\\' . $this->argument('domain') . '\\' . config('repomodel.paths.write.namespace').'\\'.config("repomodel.drivers.$driver.dir");
    }

    protected function replaceModel(string $stub)
    {
        $name  = $this->getNameInput();
        $domain  = $this->argument('domain');
        $model = 'App\\Domain\\' . $domain . '\\' .config('repomodel.paths.models.namespace').'\\'.$name;
        $alias = $name.'Model';

        if ( ! class_exists($model)) {
            $this->warn("Creating [$model].");
            $this->createModel($domain, $name);
            $this->line("Created [$model].");
        }

        return str_replace(['{{ modelNamespace }}', '{{modelNamespace}}'], "$model as $alias", str_replace(['{{ model }}', '{{model}}'], $alias, $stub));
    }

    /**
     * Resolve the fully-qualified path to the stub.
     *
     * @param  string $stub
     * @return string
     */
    protected function resolveStubPath($stub)
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
        ? $customPath
        : __DIR__.$stub;
    }
}
