<?php

namespace Dibi\ReposModelsGenerator\Console\MakeCommand;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Dibi\ReposModelsGenerator\Console\MakeCommand\Concerns\InteractsWithRepoClasses;

class ServiceProvider extends BaseMake
{
    use InteractsWithRepoClasses;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the service provider';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'repo:provider';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $class     = config('repomodel.paths.domain.namespace') . '\\' . $this->argument('domain') . '\\Providers\\RepositoryServiceProvider';
        $path      = app_path('Domain/' . $this->argument('domain') . '/Providers');
        $name      = class_basename($class);
        $classPath = $path."/$name.php";

        $this->warn("Generating [$class]");

        if (file_exists($classPath)) {
            File::delete($classPath);
        }

        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }

        Artisan::call(
            'make:provider',
            [
                'name' => $class,
            ]
        );

        if (file_exists($classPath)) {
            file_put_contents($classPath, Str::replaceFirst('{', $this->bindingsVar(), file_get_contents($classPath)));
        } else {
            $this->error("Failed to generate [$class]");
        }

        $this->line("Generated [$class]");
    }

    protected function getStub()
    {
        return $this->resolveStubPath('/stubs/provider.stub');
    }

    private function bindings()
    {
        $bindings = [];

        $read           = config('repomodel.paths.domain.namespace') . '\\' . $this->argument('domain') . '\\Repositories\\Read';
        $readContracts  = config('repomodel.paths.domain.namespace') . '\\' . $this->argument('domain') . '\\Contracts\\Repositories\\Read';
        $write          = config('repomodel.paths.domain.namespace') . '\\' . $this->argument('domain') . '\\Repositories\\Write';
        $writeContracts = config('repomodel.paths.domain.namespace') . '\\' . $this->argument('domain') . '\\Contracts\\Repositories\\Write';

        foreach (scandir(config('repomodel.paths.domain.path')) as $domain) {
            foreach (scandir(app_path($domain . 'Contracts/Repositories/Read'))  as $contract) {
                if (!Str::endsWith($contract, '.php')) {
                    continue;
                }
                $contract = $readContracts . '\\' . str_replace('.php', '', $contract);
                if (!$this->implementsReadRepo($contract)) {
                    continue;
                }

                $repo = "$read\\MySql\\" . class_basename($contract);

                if (class_exists($repo)) {
                    $bindings[$contract] = $repo;
                }
            }
        }

        foreach (scandir(config('repomodel.paths.domain.path')) as $domain) {
            foreach (scandir(app_path($domain . 'Contracts/Repositories/Write')) as $contract) {
                if (!Str::endsWith($contract, '.php')) {
                    continue;
                }
                $contract = $writeContracts . '\\' . str_replace('.php', '', $contract);
                if (!$this->implementsWriteRepo($contract)) {
                    continue;
                }

                $repo = "$write\\MySql\\" . class_basename($contract);

                if (class_exists($repo)) {
                    $bindings[$contract] = $repo;
                }
            }
        }

        return $bindings;
    }

    private function bindingsVar()
    {
        $output = '{'.PHP_EOL."\t".'public array $bindings = ['.PHP_EOL;

        foreach ($this->bindings() as $contract => $binding) {
            $output .= "\t\t\\$contract::class => \\$binding::class,".PHP_EOL;
        }

        $output .= "\t".'];'.PHP_EOL;

        return $output;
    }
}
