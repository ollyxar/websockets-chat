<?php namespace Ollyxar\WSChat;

use Illuminate\Support\ServiceProvider;
use Ollyxar\WSChat\Console\Send2ServerCommand;
use Ollyxar\WSChat\Console\ServerCommand;

/**
 * Class WSChatServiceProvider
 * @package Ollyxar\WSChat
 */
class WSChatServiceProvider extends ServiceProvider
{

    /**
     * @var bool
     */
    protected $defer = true;

    /**
     * Loading config
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
     * Register commands
     *
     * @return void
     */
    public function register(): void
    {
        $configPath = __DIR__ . '/../config/websockets-chat.php';
        $this->mergeConfigFrom($configPath, 'websockets-chat');
        
        $this->app->singleton(
            'command.websockets-chat.run',
            function ($app) {
                return new ServerCommand($app['config']);
            }
        );

        $this->app->singleton(
            'command.websockets-chat.send',
            function ($app) {
                return new Send2ServerCommand($app['config']);
            }
        );

        $this->commands('command.websockets-chat.run', 'command.websockets-chat.send');
    }

    /**
     * Define commands
     *
     * @return array
     */
    public function provides(): array
    {
        return ['command.websockets-chat.run', 'command.websockets-chat.send'];
    }
}
