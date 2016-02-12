<?php

defined('SYSPATH') or die('No direct script access.');
/**
 * SSDB PHP session class.
 *
 * @package    Kohana
 * @category   Session
 * @author     Dariusz Rorat
 * @copyright  (c) 2015 Dariusz Rorat
 */

class Kohana_Session_SSDB extends Session_Common
{

    /**
     * Constructs the redis session driver. This method cannot be invoked externally. The redis session driver must
     * be instantiated using the `Session::instance()` method.
     *
     * @param   array  $config  config
     */
    public function __construct(array $config, $id = null)
    {
        require_once Kohana::find_file('vendor/SSDB', 'SSDB');

        // Setup parent
        $host = $config['host'];
        $port = $config['port'];
        $timeout = $config['timeout'];

        if (isset($config['lifetime']))
        {
            // Session lifetime
            $this->_lifetime = (int) $config['lifetime'];
        }

        $this->_connection = new SimpleSSDB($host, $port, $timeout);
        $this->_handler = new Session_Handler_SSDB(
                $this->_connection, $this->_lifetime
                );

        $this->_handler->register();

        parent::__construct($config, $id);

    }

}

// End Session_SSDB
