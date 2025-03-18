<?php

it(
    'creates a read and write repo contracts',
    function (string $command, string $filename, string $destDirConfigKey) {
        $destDir      = config($destDirConfigKey);
        $expectedFile = "$destDir/$filename.php";
        $modelPath    = app_path('/Models/'.$filename.'.php');

        if (file_exists($expectedFile)) {
            echo "[$expectedFile] already exists. Deleting it.";
            File::delete($expectedFile);
        }

        $this->artisan(
            $command,
            [
                'name'    => $filename,
                '--force' => false,
            ]
        );

        expect($destDir)->toBeString();
        expect($expectedFile)->toBeFile();
        expect($modelPath)->toBeFile();

        File::delete($expectedFile);
        $this->assertFileDoesNotExist($expectedFile);

        File::delete($modelPath);
        $this->assertFileDoesNotExist($modelPath);
    }
)->with(
    [
        ['make:readrepocontract', 'Test', 'repomodel.paths.read.contract_path'],
        ['make:writerepocontract', 'Test', 'repomodel.paths.write.contract_path'],
    ]
)->skip();

it(
    'creates a read and write repos',
    function (string $command, string $filename, string $destDirConfigKey, array $filesExpectedToCreate) {
        foreach ($filesExpectedToCreate as $expectedFile) {
            if (file_exists($expectedFile)) {
                echo "[$expectedFile] already exists. Deleting it.";
                File::delete($expectedFile);
            }
        }

        $art = $this->artisan(
            $command,
            [
                'name'    => $filename,
                '--force' => false,
            ]
        )->assertExitCode(0);

        $destDir = config($destDirConfigKey);
        expect($destDir)->toBeString();
        $expectedFile = "$destDir/$filename.php";

        expect($expectedFile)->toBeFile();

        //    $instance = require $expectedFile;
        //    $this->assertEquals($instance::model, 'App\\Models\\Test');

        array_push($filesExpectedToCreate, $expectedFile);

        foreach ($filesExpectedToCreate as $file) {
            expect($file)->toBeFile();
            File::delete($file);
            $this->assertFileDoesNotExist($file);
        }
    }
)->with(
    [
        [
            'make:abstractreadrepo',
            'Test',
            'repomodel.paths.read.path',
            [
                __DIR__.'/../../../../app/Contracts/Repositories/Read/Test.php',
                __DIR__.'/../../../../app/Models/Test.php',
            ],
        ],
        [
            'make:abstractwriterepo',
            'Test',
            'repomodel.paths.write.path',
            [
                __DIR__.'/../../../../app/Contracts/Repositories/Write/Test.php',
                __DIR__.'/../../../../app/Models/Test.php',
            ],
        ],
    ]
)->skip();
