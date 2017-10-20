<?php namespace Ollyxar\WSChat;

use Illuminate\Support\ServiceProvider;
use Ollyxar\WSChat\Console\Send2ServerCommand;
use Ollyxar\WSChat\Console\ServerCommand;

class WSChatServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        
        $configPath = __DIR__ . '/../config/websockets-chat.php';
        if (function_exists('config_path')) {
            $publishPath = config_path('websockets-chat.php');
        } else {
            $publishPath = base_path('config/websockets-chat.php');
        }
        $this->publishes([$configPath => $publishPath], 'config');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $configPath = __DIR__ . '/../config/websockets-chat.php';
        $this->mergeConfigFrom($configPath, 'ide-helper');
        
        $this->app->singleton(
            'command.websockets-chat.run',
            function ($app) {
                return new ServerCommand($app['config']);
            }
        );

        $this->app->singleton(
            'command.iwebsockets-chat.send',
            function ($app) {
                return new Send2ServerCommand($app['config']);
            }
        );

        $this->commands('command.websockets-chat.run', 'command.websockets-chat.send');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['command.websockets-chat.run', 'command.websockets-chat.send'];
    }
}
