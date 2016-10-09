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

include Kohana::find_file('vendor/SSDB', 'SSDB');

class Kohana_Session_SSDB extends Session_Nosql
{

    /**
     * Constructs the SSDB session driver. This method cannot be invoked externally. The SSDB session driver must
     * be instantiated using the `Session::instance()` method.
     *
     * @param   array  $config  config
     */
    public function __construct(array $config, $id = null)
    {

        if (!interface_exists('SessionHandlerInterface'))
        {
            throw new Session_Exception(
            "The session handler implemented by SSDB needs PHP >= 5.4.0 or a polyfill " .
            "for \SessionHandlerInterface either provided by you or an external package.\n");
        }

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
