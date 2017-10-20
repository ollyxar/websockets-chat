<?php

return [
    /**
     * Class handler
     */
    'handler'      => '\App\Handler',

    /**
     * Host
     * @var string
     */
    'host'         => '0.0.0.0',

    /**
     * Port
     * @var int
     */
    'port'         => 2083,

    /**
     * Count of workers
     * @var int
     */
    'worker_count' => 1,

    /**
     * Secure connection
     * @var bool
     */
    'use_ssl'      => false,

    /**
     * PEM certificate
     * @var string
     */
    'cert'         => '/etc/nginx/conf.d/wss.pem',

    /**
     * PEM certificate pass phrase
     * @var string
     */
    'pass_phrase'  => 'abracadabra',
];
