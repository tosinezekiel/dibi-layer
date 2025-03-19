<?php

namespace Dibi\ReposModelsGenerator\Console\MakeCommand;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Dibi\ReposModelsGenerator\Console\MakeCommand\Concerns\CallsMakeCommands;
use Dibi\ReposModelsGenerator\Console\MakeCommand\Concerns\InteractsWithRepoClasses;

class ForceRecreateAll extends Command
{
    use CallsMakeCommands;
    use InteractsWithRepoClasses;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recreate a new Model, Interface and Read/Write Repositories';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'force-recreate:repos {--create=all : Example args: AbstractWriteRepo, ReadRepoContract}';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $create = $this->option('create');

        $this->generateServiceProvider();

        foreach ($this->getRepos() as $contract) {
            if ( ! $this->isRepo($contract)) {
                continue;
            }

            $name = class_basename($contract);

            if ('all' === $create) {
                $this->createModel($name, true);
                $this->createReadRepoContract($name, true);
                $this->createWriteRepoContract($name, true);
                $this->createReadRepos($name, true);
                $this->createWriteRepos($name, true);

                continue;
            }

            if ($this->isRead($contract, $create) || $this->isWrite($contract, $create)) {
                $this->{self::method($create)}($name, true);
            }
        }

        $this->generateServiceProvider();
    }

    private function getRepos(): array
    {
        $class = config('repomodel.paths.provider.namespace');

        if ( ! class_exists($class)) {
            throw new \Exception("Class [$class] was not found");
        }

        return collect(array_keys((new $class('fake app'))->bindings ?? []))
            ->filter()
            ->all();
    }

    private function isRead(string $contract, string $typeToCreate)
    {
        return Str::contains($typeToCreate, 'Read') && $this->implementsReadRepo($contract);
    }

    private function isWrite(string $contract, string $typeToCreate)
    {
        return Str::contains($typeToCreate, 'Write') && $this->implementsWriteRepo($contract);
    }

    private static function method(string $name)
    {
        return 'create'.ucfirst(Str::camel($name));
    }
}
