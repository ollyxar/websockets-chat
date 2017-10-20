<?php namespace Ollyxar\WSChat\Console;

use Illuminate\Console\Command;

class Send2ServerCommand extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'websockets-chat:send';

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
    public function handle()
    {
        $this->info("hurray send!");
    }
}
