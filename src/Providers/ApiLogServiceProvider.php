<?php

namespace RMN\Providers;

use RMN\Console\Commands\ClearApiLogger;
use RMN\Http\Exceptions\InvalidApiLogDriverException;
use RMN\Http\Middleware\ApiLogger;
use RMN\Contracts\ApiLoggerInterface;
use RMN\DBLogger;
use RMN\FileLogger;
use Exception;
use Illuminate\Support\ServiceProvider;

class ApiLogServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     * @throws \Exception
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/apilog.php', 'apilog'
        );
        $this->bindServices();
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadConfig();
        $this->loadRoutes();
        $this->loadViews();
        $this->loadCommand();
        $this->loadMigrations();
    }

    public function bindServices(){
        $driver = config('apilog.driver');
        $instance = "";
        switch ($driver) {
            case 'file':
                $instance = FileLogger::class;
                break;
            case 'db':
                $instance = DBLogger::class;
                break;
            default:
                try {
                    $instance = $driver;
                    if(!(resolve($instance) instanceof ApiLoggerInterface))
                    {
                        throw new InvalidApiLogDriverException();
                    }
                }
                catch(\ReflectionException $exception){
                    throw new InvalidApiLogDriverException();
                }
                break;
        }
        $this->app->singleton(ApiLoggerInterface::class,$instance);

        $this->app->singleton('apilogger', function ($app) use ($instance){
            return new ApiLogger($app->make($instance));
        });
    }

    public function loadConfig(){
        $this->publishes([
            __DIR__.'/../../config/apilog.php' => config_path('apilog.php')
        ], 'config');
    }

    public function loadRoutes(){
        $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');
    }

    public function loadViews(){
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'apilog');
    }

    public function loadCommand(){
        $this->commands([
            ClearApiLogger::class
        ]);
    }

    public function loadMigrations(){
        if(config('apilog.driver') === 'db')
            $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
    }
}
