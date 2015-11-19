<?php

defined('SYSPATH') or die('No direct script access.');
/**
 * Redis PHP session class.
 *
 * @package    Kohana
 * @category   Session
 * @author     Dariusz Rorat
 * @copyright  (c) 2015 Dariusz Rorat
 */
require_once Kohana::find_file('vendor/predis', 'autoload');

class Kohana_Session_Redis extends Session
{

    /**
     * Redis client
     */
    protected $_client;

    /**
     * Session save handler
     */
    protected $_handler;

    /**
     * Constructs the redis session driver. This method cannot be invoked externally. The redis session driver must
     * be instantiated using the `Session::instance()` method.
     *
     * @param   array  $config  config
     */
    public function __construct(array $config, $id = null)
    {
        if (!interface_exists('SessionHandlerInterface'))
        {
            throw new Session_Exception(
            "The session handler implemented by Predis needs PHP >= 5.4.0 or a polyfill " .
            "for \SessionHandlerInterface either provided by you or an external package.\n");
        }

        // Setup parent
        $single_server = array(
            'host' => $config['host'],
            'port' => $config['port'],
            'database' => $config['database']
        );

        if (isset($config['lifetime']))
        {
            // Session lifetime
            $this->_lifetime = (int) $config['lifetime'];
        }

        $this->_client = new Predis\Client(
                $single_server, array('prefix' => 'sessions:')
        );
        $this->_handler = new Predis\Session\SessionHandler(
                $this->_client, array('gc_maxlifetime' => $this->_lifetime)
        );
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
