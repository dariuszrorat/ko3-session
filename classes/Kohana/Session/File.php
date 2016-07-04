<?php

defined('SYSPATH') or die('No direct script access.');
/**
 * File PHP session class.
 *
 * @package    Kohana
 * @category   Session
 * @author     Dariusz Rorat
 * @copyright  (c) 2015 Dariusz Rorat
 */

class Kohana_Session_File extends Session
{
	// Garbage collection requests
	protected $_gc = 500;

        protected $_save_path;

	// The current session id
	protected $_session_id;

	public function __construct(array $config = NULL, $id = NULL)
	{
		if (isset($config['gc']))
		{
			// Set the gc chance
			$this->_gc = (int) $config['gc'];
		}

                $this->_save_path = $config['save_path'];

		parent::__construct($config, $id);

		if (mt_rand(1, $this->_gc) === $this->_gc)
		{
			// Run garbage collection
			// This will average out to run once every X requests
			$this->_gc();
		}
	}

	public function id()
	{
		return $this->_session_id;
	}

	protected function _read($id = NULL)
	{
		if ($id OR $id = Cookie::get($this->_name))
		{
                    $this->_session_id = $id;
                    $file = sha1($id) . '.sess';
                    $dir = $this->_save_path . DIRECTORY_SEPARATOR
                        . $file[0] . $file[1] . DIRECTORY_SEPARATOR;
                    if (realpath($dir.$file))
                    {
                        $result = file_get_contents($dir.$file);
                        return $result;
                    }
                    else
                    {
                        return NULL;
                    }
		}

		// Create a new session id
		$this->_regenerate();

		return NULL;
	}

	protected function _regenerate()
	{
            $id = uniqid(NULL, TRUE);
	    return $this->_session_id = $id;
	}

	protected function _write()
	{
            $file = sha1($this->_session_id). '.sess';
            $dir = $this->_save_path . DIRECTORY_SEPARATOR . $file[0] . $file[1] . DIRECTORY_SEPARATOR;
	    if ( ! is_dir($dir))
	    {
	        // Create the cache directory
		mkdir($dir, 0777, TRUE);

		// Set permissions (must be manually set to fix umask issues)
		chmod($dir, 0777);
	    }

            file_put_contents($dir.$file, $this->__toString(), LOCK_EX);

	    // Update the cookie with the new session id
	    Cookie::set($this->_name, $this->_session_id, $this->_lifetime);

	    return TRUE;
	}

	/**
	 * @return  bool
	 */
	protected function _restart()
	{
		$this->_regenerate();

		return TRUE;
	}

	protected function _destroy()
	{
            $file = sha1($id) . '.sess';
            $dir = $this->_save_path . DIRECTORY_SEPARATOR
                        . $file[0] . $file[1] . DIRECTORY_SEPARATOR;
            if (realpath($dir.$file))
            {
                unlink($dir.$file);
                Cookie::delete($this->_name);
                return TRUE;
            }
            else
            {
                return FALSE;
            }
	}

	protected function _gc()
	{
	    if ($this->_lifetime)
	    {
	        // Expire sessions when their lifetime is up
		$expires = $this->_lifetime;
	    }
	    else
	    {
	        // Expire sessions after one month
		$expires = Date::MONTH;
	    }

            $now = time();

                for ($i = 0; $i < 256; $i++)
                {
                    $subdir = sprintf('%02x', $i);
                    $file = new SplFileInfo($this->_save_path . DIRECTORY_SEPARATOR . $subdir);
                    if ($file->isDir())
                    {
                      $files = new DirectoryIterator($file->getPathname());
		      while ($files->valid())
		      {
		          $name = $files->getFilename();

			  if ($name != '.' AND $name != '..')
			  {
			      $fp = new SplFileInfo($files->getRealPath());
                              if (($now - $fp->getMTime()) > $expires)
                              {
                                  unlink($fp->getRealPath());
                              }
			  }

			  $files->next();
		      }

                    }
                }

	}

}

// End Session_File
