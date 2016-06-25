<?php

defined('SYSPATH') or die('No direct script access.');

return array(
    'file' => array(
        'name' => 'session_file',
        'encrypted' => False,
        'save_path' => APPPATH . 'sessions',
        'lifetime' => 3600,
        'gc' => 500,
    ),
    'redis' => array(
        'name' => 'session_redis',
        'encrypted' => False,
        'host' => '127.0.0.1',
        'port' => 6379,
        'database' => 15,
        'lifetime' => 3600,
    ),
    'SSDB' => array(
        'name' => 'session_ssdb',
        'encrypted' => False,
        'host' => '127.0.0.1',
        'port' => 8888,
        'timeout' => 2000,
        'lifetime' => 3600,
    ),
);
