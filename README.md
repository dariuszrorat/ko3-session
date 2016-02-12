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
    'cookie' => array(
        'name' => 'session_cookie',
        'encrypted' => False,
        'lifetime' => 30,
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
    'sqlite' => array(
        'name' => 'session_sqlite',
        'encrypted' => False,
        'database' => APPPATH . 'sessions/kohana-sessions.sql3',
        'schema' => 'CREATE TABLE sessions(id integer PRIMARY KEY AUTOINCREMENT, session_id VARCHAR(50), session_data TEXT, last_activity datetime, expires datetime)',
        'lifetime' => 3600,
    ),
    'mysql' => array(
        'name' => 'session_mysql',
        'encrypted' => False,
        'hostname' => 'localhost',
        'database' => 'mydb',
        'username' => 'root',
        'password' => '',
        'schema' =>
        'CREATE TABLE sessions (' .
        'id int(10) unsigned NOT NULL AUTO_INCREMENT,' .
        'session_id varchar(50) NOT NULL,' .
        'session_data text NOT NULL,' .
        'last_activity INT(11) NOT NULL,' .
        'expires INT(11) NOT NULL,' .
        'PRIMARY KEY (id)' .
        ') ENGINE=InnoDB DEFAULT CHARSET=utf8;',
        'lifetime' => 3600,
    ),
    'postgresql' => array(
        'name' => 'session_postgresql',
        'encrypted' => False,
        'hostname' => 'localhost',
        'database' => 'mydb',
        'username' => 'postgres',
        'password' => '',
        'schema' =>
        'CREATE TABLE sessions' .
        '(' .
        '  id serial NOT NULL,' .
        '  session_id character varying(50) NOT NULL,' .
        '  session_data text NOT NULL,' .
        '  last_activity bigint NOT NULL,' .
        '  expires bigint NOT NULL,' .
        '  CONSTRAINT pk_sessions PRIMARY KEY (id)' .
        ')',
        'lifetime' => 3600,
    ),
);

```
