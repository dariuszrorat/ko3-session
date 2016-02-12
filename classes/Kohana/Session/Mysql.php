<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * MySQL PHP session class.
 *
 * @package    Kohana
 * @category   Session
 * @author     Dariusz Rorat
 * @copyright  (c) 2015 Dariusz Rorat
 */
class Kohana_Session_Mysql extends Session_Common
{

    /**
     * Constructs the session driver. This method cannot be invoked externally. The session driver must
     * be instantiated using the `Session::instance()` method.
     *
     * @param   array  $config  config
     */
    public function __construct(array $config, $id = null)
    {
        $database = Arr::get($config, 'database', NULL);
        $hostname = Arr::get($config, 'hostname', NULL);
        $username = Arr::get($config, 'username', NULL);
        $password = Arr::get($config, 'password', NULL);
        $dsn = 'mysql:host=' . $hostname . ';dbname=' . $database;
        // Load new Mysql DB
        $this->_connection = new PDO($dsn, $username, $password);

        // Test for existing DB
        $result = $this->_connection->query("SELECT * FROM information_schema.tables WHERE table_schema = '" . $database . "' AND table_name = 'sessions' LIMIT 1;")->fetchAll();

        // If there is no table, create a new one
        if (0 == count($result))
        {
            $database_schema = Arr::get($config, 'schema', NULL);

            if ($database_schema === NULL)
            {
                throw new Session_Exception('Database schema not found in Kohana Session configuration');
            }

            try
            {
                // Create the table
                $this->_connection->query(Arr::get($config, 'schema', NULL));
            } catch (PDOException $e)
            {
                throw new Session_Exception('Failed to create new MySQL table with the following error : :error', array(':error' => $e->getMessage()));
            }
        }

        if (isset($config['lifetime']))
        {
            // Session lifetime
            $this->_lifetime = (int) $config['lifetime'];
        }

        $this->_handler = new Session_Handler_Database($this->_connection, $this->_lifetime);
        $this->_handler->register();

        parent::__construct($config, $id);
    }


}

// End Session_Mysql
