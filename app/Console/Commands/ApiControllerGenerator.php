<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class ApiControllerGenerator extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:api-controller
                            {name : Name of the controller}
                            {--ver=1 : API version this controller is being created for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new API controller class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'API controller';

    /**
     * Get the stub file for generator
     *
     * @return string
     */
    protected function getStub(): string
    {
        return __DIR__ . '/stubs/api_controller.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace
     *
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        $version = $this->option('ver');
        $version = is_numeric($version) ? $version : 1;

        return $rootNamespace . '\\Http\\Controllers\\API\\v' . $version;
    }
}
