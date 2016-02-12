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

class Kohana_Session_Redis extends Session_Common
{

    /**
     * Constructs the redis session driver. This method cannot be invoked externally. The redis session driver must
     * be instantiated using the `Session::instance()` method.
     *
     * @param   array  $config  config
     */
    public function __construct(array $config, $id = null)
    {
        require_once Kohana::find_file('vendor/predis', 'autoload');

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

        $this->_connection = new Predis\Client(
                $single_server, array('prefix' => 'sessions:')
        );
        $this->_handler = new Predis\Session\SessionHandler(
                $this->_connection, array('gc_maxlifetime' => $this->_lifetime)
        );
        $this->_handler->register();

        parent::__construct($config, $id);
    }

}

// End Session_Redis
