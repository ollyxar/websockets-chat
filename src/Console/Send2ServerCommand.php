<?php namespace Ollyxar\WSChat\Console;

use Illuminate\Console\Command;
use Ollyxar\WebSockets\Server as WServer;
use Ollyxar\WebSockets\Frame;

/**
 * Class Send2ServerCommand
 * @package Ollyxar\WSChat\Console
 */
class Send2ServerCommand extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'websockets-chat:send';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'websockets-chat:send {message}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'WebSockets Chat direct messaging.';

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
        $socket = socket_create(AF_UNIX, SOCK_STREAM, 0);
        socket_connect($socket, WServer::$connector);

        $data = new \stdClass();
        $data->type = 'system';
        $data->message = $this->argument('message');
        $msg = Frame::encode(json_encode($data));

        socket_write($socket, $msg);
        socket_close($socket);
        $this->info("Message sent.");
    }
}
