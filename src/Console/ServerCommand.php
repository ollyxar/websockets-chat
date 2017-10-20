<?php namespace Ollyxar\WSChat\Console;

use Illuminate\Console\Command;
use Ollyxar\WebSockets\Server;

class ServerCommand extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'websockets-chat:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'WebSockets Chat server starter.';

    /**
     * @var \Illuminate\Config\Repository
     */
    protected $config;

    /**
     * @param \Illuminate\Config\Repository $config
     */
    public function __construct($config) {
        $this->config = $config;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->info("starting server...");

        $server = new Server(
            $this->config->get('websockets-chat.host'),
            $this->config->get('websockets-chat.port'),
            $this->config->get('websockets-chat.worker_count'),
            $this->config->get('websockets-chat.use_ssl')
        );

        if ($this->config->get('websockets-chat.use_ssl')) {
            $this->info("Setting up SSL...");
            $server
                ->setCert($this->config->get('websockets-chat.cert'))
                ->setPassPhrase($this->config->get('websockets-chat.pass_phrase'));
        }

        $server->setHandler($this->config->get('websockets-chat.handler'));
        $server->run();
    }
}
