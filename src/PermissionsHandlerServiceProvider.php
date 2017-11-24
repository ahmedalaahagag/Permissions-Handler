<?php

namespace PermissionsHandler;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class PermissionsHandlerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        require_once __DIR__.'/Blade/Directives.php';

        // register console commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                \PermissionsHandler\Commands\AddCommand::class,
                \PermissionsHandler\Commands\AssignCommand::class,
                \PermissionsHandler\Commands\SeederCommand::class,
                \PermissionsHandler\Commands\ClearAnnotationsCache::class,
            ]);
        }

        $this->publishes([
            __DIR__.'/Migrations/migrations.php' => base_path('database/migrations/'.date('Y_m_d_His').'_create_permissions_migrations.php'),
            __DIR__.'/Config'                    => base_path('config'),
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('permissionsHandler', function () {
            return new PermissionsHandler();
        });
        // register annotation
        AnnotationRegistry::registerFile(__DIR__.'/Annotations/Permissions.php');
        AnnotationRegistry::registerFile(__DIR__.'/Annotations/Roles.php');
        AnnotationRegistry::registerFile(__DIR__.'/Annotations/Owns.php');
    }
}
