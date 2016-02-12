<?php

defined('SYSPATH') or die('No direct script access.');

/**
 * Sqlite PHP session class.
 *
 * @package    Kohana
 * @category   Session
 * @author     Dariusz Rorat
 * @copyright  (c) 2015 Dariusz Rorat
 */
class Kohana_Session_Sqlite extends Session_Common
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
        if ($database === NULL)
        {
            throw new Session_Exception('Database path not available in Kohana Session configuration');
        }
        // Load new Sqlite DB
        $this->_connection = new PDO('sqlite:' . $database);

        // Test for existing DB
        $result = $this->_connection->query("SELECT * FROM sqlite_master WHERE name = 'sessions' AND type = 'table'")->fetchAll();

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
                // Create the caches table
                $this->_connection->query(Arr::get($config, 'schema', NULL));
            } catch (PDOException $e)
            {
                throw new Session_Exception('Failed to create new SQLite sessions table with the following error : :error', array(':error' => $e->getMessage()));
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

// End Session_Sqlite
