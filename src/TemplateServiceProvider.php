<?php

namespace Lcloss\Template;

use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\ServiceProvider;
use Lcloss\Template\Console\Commands\BuildCommand;

class TemplateServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        AboutCommand::add('Lcloss\Template', fn() => ['Version' => '1.0.0']);

        /* Register commands */
        if ($this->app->runningInConsole()) {
            $this->commands([
                BuildCommand::class,
            ]);
        }

        // Registrar as rotas do package
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
    }
}
