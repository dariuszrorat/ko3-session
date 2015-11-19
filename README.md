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

## Usage

Copy:
```
  session/*.php to Your application/classes/session/*.php
  session/handler/database.php to Your application/classes/session/handler/database.php
  kohana/session/*.php to application/classes/kohana/session/*.php
  kohana/session/handler/database.php to Your application/classes/kohana/session/handler/database.php
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
        'database' => 'mojastrona',
        'username' => 'root',
        'password' => 'root',
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
        'database' => 'finance',
        'username' => 'postgres',
        'password' => 'postgres',
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
