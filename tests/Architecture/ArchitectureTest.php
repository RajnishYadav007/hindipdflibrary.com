<?php

namespace Tests\Architecture;

use Illuminate\Database\Eloquent\Model;
use Tests\TestCase;

class ArchitectureTest extends TestCase
{
    /**
     * Test that foundational application architectural directories exist.
     */
    public function test_architecture_directories_exist(): void
    {
        $directories = [
            app_path('Actions'),
            app_path('Services'),
            app_path('Policies'),
            app_path('Support'),
            app_path('Http/Controllers'),
            app_path('Livewire'),
            app_path('Models'),
        ];

        foreach ($directories as $directory) {
            $this->assertDirectoryExists($directory, "Architectural directory [{$directory}] must exist.");
        }
    }

    /**
     * Test that all classes in app/Models extend Eloquent Model.
     */
    public function test_all_models_extend_eloquent_model(): void
    {
        $modelFiles = glob(app_path('Models/*.php'));

        $this->assertNotEmpty($modelFiles, 'app/Models must contain model classes.');

        foreach ($modelFiles as $file) {
            $className = 'App\\Models\\' . basename($file, '.php');

            $this->assertTrue(
                class_exists($className),
                "Model class [{$className}] must be loadable."
            );

            $this->assertTrue(
                is_subclass_of($className, Model::class),
                "Model [{$className}] must extend Eloquent Model or Authenticatable."
            );
        }
    }

    /**
     * Test that no raw debugging statements (dd, dump, var_dump) exist in app/.
     */
    public function test_no_debugging_statements_in_application_code(): void
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(app_path())
        );

        $forbiddenFunctions = ['dd(', 'dump(', 'var_dump('];

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $content = file_get_contents($file->getRealPath());

                foreach ($forbiddenFunctions as $forbidden) {
                    $this->assertStringNotContainsString(
                        $forbidden,
                        $content,
                        "Application file [{$file->getFilename()}] contains forbidden debug statement [{$forbidden}]."
                    );
                }
            }
        }
    }

    /**
     * Test that the /admin endpoint remains guarded and does not allow unauthenticated access.
     */
    public function test_admin_route_is_protected(): void
    {
        $response = $this->get('/admin');

        // Filament redirects unauthenticated guests to login
        $response->assertRedirect('/admin/login');
    }
}
