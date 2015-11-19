<?php

defined('SYSPATH') or die('No direct script access.');

class Kohana_Session_Handler_Database
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
        $now = time();
        $last_activity = $now - $maxlifetime;

        $statement = $this->_connection->prepare("DELETE * FROM sessions WHERE last_activity < :last_activity");

        try
        {
            $statement->execute(array(':last_activity' => $last_activity));
        } catch (PDOException $e)
        {
            throw new Session_Exception('There was a problem querying the local SQLite3 database. :error', array(':error' => $e->getMessage()));
        }

        return true;
    }

    /**
     *
     */
    public function read($session_id)
    {
        $now = time();
        $expires = $now + $this->_ttl;

        $statement = $this->_connection->prepare("SELECT * FROM sessions WHERE session_id = :session_id AND expires <= :expires");

        try
        {
            $statement->execute(array(':session_id' => $session_id, ':expires' => $expires));
        } catch (PDOException $e)
        {
            throw new Session_Exception('There was a problem querying the local SQLite3 database. :error', array(':error' => $e->getMessage()));
        }

        if ($result = $statement->fetch(PDO::FETCH_OBJ))
        {
            return $result->session_data;
        } else
        {
            return NULL;
        }
    }

    /**
     *
     */
    public function write($session_id, $session_data)
    {
        $now = time();
        $expires = $now + $this->_ttl;

        $data = $this->read($session_id, $expires);
        if ($data === null)
        {
            $statement = $this->_connection->prepare("INSERT INTO sessions (session_id, session_data, last_activity, expires) VALUES (:session_id, :session_data, :last_activity, :expires)");

            try
            {
                $statement->execute(array(':session_id' => $session_id, ':session_data' => $session_data, ':last_activity' => $now, ':expires' => $expires));
            } catch (PDOException $e)
            {
                throw new Session_Exception('There was a problem querying the local SQLite3 database. :error', array(':error' => $e->getMessage()));
            }
        } else
        {
            $statement = $this->_connection->prepare("UPDATE sessions SET session_data = :session_data, last_activity = :last_activity, expires = :expires WHERE session_id = :session_id ");

            try
            {
                $statement->execute(array(':session_id' => $session_id, ':session_data' => $session_data, ':last_activity' => $now, ':expires' => $expires));
            } catch (PDOException $e)
            {
                throw new Session_Exception('There was a problem querying the local SQLite3 database. :error', array(':error' => $e->getMessage()));
            }
        }

        return true;
    }

    /**
     *
     */
    public function destroy($session_id)
    {
        $statement = $this->_connection->prepare("DELETE FROM sessions WHERE session_id = :session_id");

        try
        {
            $statement->execute(array(':session_id' => $this->$session_id));
        } catch (PDOException $e)
        {
            throw new Session_Exception('There was a problem querying the local SQLite3 database. :error', array(':error' => $e->getMessage()));
        }

        return true;
    }

}
