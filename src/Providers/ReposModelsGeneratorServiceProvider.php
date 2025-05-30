<?php

namespace Dibi\ReposModelsGenerator\Providers;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Dibi\ReposModelsGenerator\Console\MakeCommand\All;
use Dibi\ReposModelsGenerator\Console\MakeCommand\Model;
use Dibi\ReposModelsGenerator\Console\MakeCommand\ReadRepoContract;
use Dibi\ReposModelsGenerator\Console\MakeCommand\ReadRepos;
use Dibi\ReposModelsGenerator\Console\MakeCommand\ServiceProvider as RepoServiceProvider;
use Dibi\ReposModelsGenerator\Console\MakeCommand\WriteRepoContract;
use Dibi\ReposModelsGenerator\Console\MakeCommand\WriteRepos;

class ReposModelsGeneratorServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * @var string
     */
    protected $moduleName = 'ReposModelsGenerator';

    /**
     * @var string
     */
    protected $moduleNameLower = 'repomodel';

    private array $commands = [
        ReadRepoContract::class,
        All::class,
        WriteRepoContract::class,
        Model::class,
        ReadRepos::class,
        WriteRepos::class,
        RepoServiceProvider::class,
    ];

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array_merge(
            $this->commands,
            [
                'command.model.make',
            ]
        );
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerConfig();
        $this->commands($this->commands);
        $this->registerModelMakeCommand();
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes(
            [
                __DIR__ . '/../Config/config.php' => config_path($this->moduleNameLower . '.php'),
            ],
            'config'
        );
        $this->mergeConfigFrom(
            __DIR__ . '/../Config/config.php',
            $this->moduleNameLower
        );
    }

    protected function registerModelMakeCommand()
    {
        $this->app->extend(
            'command.model.make',
            function ($service, $app) {
                return new Model($app['files']);
            }
        );
    }
}
