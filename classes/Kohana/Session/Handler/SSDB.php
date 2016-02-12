<?php

defined('SYSPATH') or die('No direct script access.');

class Kohana_Session_Handler_SSDB
{

    protected $_connection;
    protected $_ttl;

    public function __construct($connection, $lifetime)
    {
        $this->_connection = $connection;
        $this->_ttl = (int) $lifetime;
    }

    /**
     * Registers the handler instance as the current session handler.
     */
    public function register()
    {
            session_set_save_handler(
                    array($this, 'open'),
                    array($this, 'close'),
                    array($this, 'read'),
                    array($this, 'write'),
                    array($this, 'destroy'),
                    array($this, 'gc')
            );
    }

    /**
     *
     */
    public function open($save_path, $session_id)
    {
        // do nothing
        return true;
    }

    /**
     *
     */
    public function close()
    {
        // do nothing
        return true;
    }

    /**
     *
     */
    public function gc($maxlifetime)
    {
        // do nothing
        return true;
    }

    /**
     *
     */
    public function read($session_id)
    {
        $now = time();
        $expires = $now + $this->_ttl;

        if (($serialized = $this->_connection->get('sess_'.$session_id)) !== NULL)
        {
            $data = unserialize($serialized);
            if ($data['expires'] < $expires)
            {
                return $data['data'];
            }
            else
            {
                return NULL;
            }
        }

        return NULL;
    }

    /**
     *
     */
    public function write($session_id, $session_data)
    {
        $now = time();
        $expires = $now + $this->_ttl;

        $data = array(
                'data'          => $session_data,
                'last_activity' => $now,
                'expires'       => $expires
            );


        $this->_connection->set('sess_'.$session_id, serialize($data));

        return true;
    }

    /**
     *
     */
    public function destroy($session_id)
    {
        $this->_connection->del('sess_'.$session_id);
        return true;
    }

}
