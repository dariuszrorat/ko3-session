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
class Kohana_Session_Mysql extends Session
{

    /**
     * Database instance
     */
    protected $_db;

    /**
     * Session save handler
     */
    protected $_handler;

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
        $this->_db = new PDO($dsn, $username, $password);

        // Test for existing DB
        $result = $this->_db->query("SELECT * FROM information_schema.tables WHERE table_schema = '" . $database . "' AND table_name = 'sessions' LIMIT 1;")->fetchAll();

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
                $this->_db->query(Arr::get($config, 'schema', NULL));
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

        $this->_handler = new Session_Handler_Database($this->_db, $this->_lifetime);
        $this->_handler->register();

        parent::__construct($config, $id);
    }

    /**
     * @return  string
     */
    public function id()
    {
        return session_id();
    }

    /**
     * @param   string  $id  session id
     * @return  null
     */
    protected function _read($id = NULL)
    {
        // Sync up the session cookie with Cookie parameters
        session_set_cookie_params($this->_lifetime, Cookie::$path, Cookie::$domain, Cookie::$secure, Cookie::$httponly);

        // Do not allow PHP to send Cache-Control headers
        session_cache_limiter(FALSE);

        // Set the session cookie name
        session_name($this->_name);

        if ($id)
        {
            // Set the session id
            session_id($id);
        }

        // Start the session
        session_start();

        // Use the $_SESSION global for storing data
        $this->_data = & $_SESSION;

        return NULL;
    }

    /**
     * @return  string
     */
    protected function _regenerate()
    {
        // Regenerate the session id
        session_regenerate_id();

        return session_id();
    }

    /**
     * @return  bool
     */
    protected function _write()
    {
        // Write and close the session
        session_write_close();

        return TRUE;
    }

    /**
     * @return  bool
     */
    protected function _restart()
    {
        // Fire up a new session
        $status = session_start();

        // Use the $_SESSION global for storing data
        $this->_data = & $_SESSION;

        return $status;
    }

    /**
     * @return  bool
     */
    protected function _destroy()
    {
        // Destroy the current session
        session_destroy();

        // Did destruction work?
        $status = !session_id();

        if ($status)
        {
            // Make sure the session cannot be restarted
            Cookie::delete($this->_name);
        }

        return $status;
    }

}

// End Session_Redis
