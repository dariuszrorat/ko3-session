# Various type session storage for Kohana Framework

This sources contains Kohana redis and database storage session classes

## Requirements

For Redis session this file requires predis library:
https://github.com/nrk/predis

Install this library on:

application/vendor

autoload.php must be in:

application/vendor/predis

This library is internal included included by:

```php
require_once Kohana::find_file('vendor/predis', 'autoload');
```
No need to install Redis PHP extension module.

The SSDB driver uses SSDB PHP library from ssdb.io
and must be installed in vendor/SSDB

## Usage

Copy:
```
  Session/*.php to Your application/classes/Session/*.php
  Session/Handler/*.php to Your application/classes/Session/Handler/*.php
  Kohana/Session/*.php to application/classes/Kohana/Session/*.php
  Kohana/Session/Handler/*.php to Your application/classes/Kohana/Session/Handler/*.php
  config/session.php to application/config/session.php
```
## Config

```php
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

```
