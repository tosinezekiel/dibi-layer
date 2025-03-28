<?php

namespace Dibi\ReposModelsGenerator\Console\MakeCommand;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ServiceProvider extends BaseMake
{
    protected $description = 'Generate a repository service provider for all domains';
    protected $signature = 'repo:provider';

    public function handle()
    {
        $domains = $this->getAllDomains();

        if (empty($domains)) {
            $this->error("No domains found in app/Domain.");
            return;
        }

        $this->info("Found domains: " . implode(", ", $domains));
        $this->generateGlobalProvider($domains);
    }

    private function generateGlobalProvider(array $domains)
    {
        $namespace = config('repomodel.paths.domain.namespace') . "\\Providers";
        $providerClass = "RepositoryServiceProvider";
        $providerPath = app_path("Domain/Providers");
        $providerFile = "$providerPath/$providerClass.php";

        $this->warn("Generating [$namespace\\$providerClass]");

        if (!File::exists($providerPath)) {
            File::makeDirectory($providerPath, 0755, true);
        }


        $bindings = $this->collectBindings($domains);

        $stub = file_get_contents($this->getStub());
        $content = Str::replace(
            ['{{ namespace }}', '{{ class }}', '{{ bindings }}'],
            [$namespace, $providerClass, $this->formatBindings($bindings)],
            $stub
        );

        file_put_contents($providerFile, $content);

        $this->info("Generated [$namespace\\$providerClass]");

        $this->updateBootstrapFile();
    }

    private function formatBindings(array $bindings)
    {
        if (empty($bindings)) {
            return '';
        }

        $output = '';
        foreach ($bindings as $contract => $implementation) {
            $output .= "\t\t\\$contract::class => \\$implementation::class," . PHP_EOL;
        }

        return $output;
    }

    private function collectBindings(array $domains)
    {
        $allBindings = [];

        foreach ($domains as $domain) {
            $bindings = $this->bindings($domain);
            $allBindings = array_merge($allBindings, $bindings);
        }

        return $allBindings;
    }


    private function bindings($domain)
    {
        $bindings = [];
        $baseNamespace = config('repomodel.paths.domain.namespace') . "\\$domain";

        $paths = [
            'Read' => [
                'contracts' => "$baseNamespace\\Contracts\\Repositories\\Read",
                'contractPath' => app_path("Domain/$domain/Contracts/Repositories/Read"),
                'implPath' => app_path("Domain/$domain/Repositories/Read/MySql"),
                'implNamespace' => "$baseNamespace\\Repositories\\Read\\MySql",
            ],
            'Write' => [
                'contracts' => "$baseNamespace\\Contracts\\Repositories\\Write",
                'contractPath' => app_path("Domain/$domain/Contracts/Repositories/Write"),
                'implPath' => app_path("Domain/$domain/Repositories/Write/MySql"),
                'implNamespace' => "$baseNamespace\\Repositories\\Write\\MySql",
            ],
        ];

        foreach ($paths as $type => $config) {
            if (!File::exists($config['contractPath'])) {
                continue;
            }

            $phpFiles = glob($config['contractPath'] . '/*.php');

            $files = [];

            if (!empty($phpFiles)) {
                $files = array_map('basename', $phpFiles);
            }

            foreach ($files as $file) {
                $contractName = pathinfo($file, PATHINFO_FILENAME);
                $contract = $config['contracts'] . '\\' . $contractName;
                $implFile = $config['implPath'] . '/' . $file;
                $implClass = $config['implNamespace'] . '\\' . $contractName;

                if (File::exists($implFile)) {
                    $contractExists = class_exists($contract) || interface_exists($contract);
                    $implExists = class_exists($implClass);
                    if ($contractExists && $implExists) {
                        $bindings[$contract] = $implClass;
                    }
                }
            }
        }

        return $bindings;
    }

    private function getAllDomains()
    {
        $domainPath = app_path('Domain');
        if (!File::exists($domainPath)) {
            return [];
        }

        return array_filter(scandir($domainPath), function ($item) use ($domainPath) {
            return $item !== '.' && $item !== '..' && is_dir($domainPath . '/' . $item);
        });
    }

    private function updateBootstrapFile()
    {
        $bootstrapFile = base_path('bootstrap/providers.php');
        if (!File::exists($bootstrapFile)) {
            $this->warn("Bootstrap file not found. Skipping registration.");
            return;
        }

        $content = file_get_contents($bootstrapFile);

        $providerClass = 'App\\Domain\\Providers\\RepositoryServiceProvider::class';

        if (strpos($content, $providerClass) !== false) {
            $this->info("Provider already registered in bootstrap/providers.php");
            return;
        }

        $pattern = '/return\s*\[\s*(.*?)\s*\]\s*;/s';
        if (preg_match($pattern, $content, $matches)) {
            $currentProviders = $matches[1];
            $newProviders = $currentProviders;

            if (trim($currentProviders) !== '') {
                $newProviders = rtrim($currentProviders);
                if (substr($newProviders, -1) !== ',') {
                    $newProviders .= ',';
                }
                $newProviders .= "\n    ";
            }

            $newProviders .= $providerClass;

            $newContent = str_replace(
                $matches[0],
                "return [\n    " . $newProviders . "\n];",
                $content
            );

            file_put_contents($bootstrapFile, $newContent);
            $this->info("Registered [RepositoryServiceProvider] in bootstrap/providers.php");
        } else {
            $newContent = "<?php\n\nreturn [\n    $providerClass\n];\n";
            file_put_contents($bootstrapFile, $newContent);
            $this->info("Created bootstrap/providers.php with [RepositoryServiceProvider]");
        }
    }

    protected function getStub()
    {
        return __DIR__ . '/stubs/provider.stub';
    }
}
