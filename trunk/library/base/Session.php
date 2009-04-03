<?php

/**
 * Session
 * 
 * @category    Blotomate
 * @package     Base
 * @author      Rafael Castilho <rafael@castilho.biz>
 */
class B_Session
{
    /**
     * Session name
     *
     * @var string
     */
    private $session_name = null;

    /**
     * Session table name
     *
     * @var string
     */
    private static $table_name = 'base_session';

    /**
     * Session constructor
     *
     * @param   string  $name
     * @return  void
     */
    public function __construct($name='default')
    {
        session_start();
        $this->session_name = $name;
    }

    /**
     * As of PHP 5.0.5 the write  and close  handlers are called after object
     * destruction and therefore cannot use objects or throw exceptions. The object
     * destructors can however use sessions.
     * 
     * It is possible to call session_write_close() from the destructor to
     * solve this chicken and egg problem. 
     *
     * @see http://br2.php.net/manual/en/function.session-set-save-handler.php
     */
    public function __destruct() { session_write_close(); }

    /**
     * Get overloading (session attribute)
     *
     * @param   string  $name
     * @return  mixed
     */
    protected function __get($name)
    {
        $value = null;

        if(array_key_exists($this->session_name, $_SESSION))
        {
            if(array_key_exists($name, $_SESSION[$this->session_name]))
            {
                $value = $_SESSION[$this->session_name][$name];
            }
        }

        return $value;
    }

    /**
     * Set overloading (session attribute)
     *
     * @param   string  $name
     * @param   mixed   $value
     * @return  void
     */
    protected function __set($name, $value)
    {
        if(is_array($_SESSION[$this->session_name]))
        {
            $_SESSION[$this->session_name][$name] = $value;
        }
    }

    /**
     * Open the session
     *
     * @return bool
     */
    public static function open ()
    {
        return true;
    }

    /**
     * Close the session
     *
     * @return bool
     */
    public static function close ()
    {
        return true;
    }

    /**
     * Read the session
     *
     * @param   string  $id
     * @return  string
     */
    public static function read ($id)
    {
        $result = B_Model::selectRow("SELECT session_data " . 
                                      "FROM " . self::$table_name . " " .
                                      "WHERE id = ?", array($id));
        return is_object($result) ? $result->session_data : '';
    }

    /**
     * Write the session
     *
     * @param   string      $id
     * @param   string      $data
     * @return  boolean
     */
    public static function write($id, $data)
    {
        $result = B_Model::selectRow("SELECT COUNT(*) AS total " . 
                                      "FROM " . self::$table_name . " " .
                                      "WHERE id = ?", array($id));

        return B_Model::execute
        (
            ($result->total > 0) ? 
                "UPDATE " . self::$table_name . " SET " .
                "session_expires = ?, session_data = ? " .
                "WHERE id = ?" :
                "INSERT INTO " . self::$table_name . " VALUES (?, ?, ?, 0)",
            ($result->total > 0) ?
                array(time(), $data, $id) :
                array($id, time(), $data)
        );
    }

    /**
     * Destroy the session
     *
     * @param   string  $id
     * @return  boolean
     */
    public static function destroy ($id) 
    {
        return B_Model::execute("DELETE FROM " . self::$table_name . " WHERE id = ?",
                                 array($id));
    }

    /**
     * Garbage Collector
     *
     * @param   integer     $life 
     * @return  boolean
     */
    public static function gc ($max) 
    {
        $registry = B_Registry::singleton();
        $expiration = intval($registry->session()->expiration);

        if(($expiration = intval($registry->session()->expiration)) <= 0)
        {
            $_m = "session expiration value must be greater than zero";
            $_d = array('method' => __METHOD__);
            throw new B_Exception($_m, E_USER_ERROR, $_d);
        }

        return B_Model::execute("DELETE FROM " . self::$table_name . " " .
                                 "WHERE (session_expires < ? AND session_active = 0) " .
                                 "OR (session_expires < ? AND session_active = 1)",
                                 array((time() - $max), (time() - $expiration)));
    }

    /**
     * Session handler register
     *
     * @return  void
     */
    public static function register ()
    {
        ini_set('session.save_handler', 'user');

        session_set_save_handler(array('B_Session', 'open'),
                                 array('B_Session', 'close'),
                                 array('B_Session', 'read'),
                                 array('B_Session', 'write'),
                                 array('B_Session', 'destroy'),
                                 array('B_Session', 'gc')
        );
    }

    /**
     * Switch session activity
     *
     * @param   boolean     $active
     * @return  boolean
     */
    public function setActive($active=true)
    {
        if($active == true)
        {
            $_SESSION[$this->session_name] = array();
        }
        else
        {
            unset($_SESSION[$this->session_name]);
        }

        return B_Model::execute("UPDATE " . self::$table_name . " " .
                                 "SET active = ? WHERE id = ?",
                                 array($active, session_id()));
    }

    /**
     * Get session activity
     *
     * @return  boolean
     */
    public function getActive()
    {
        $result = B_Model::selectRow("SELECT COUNT(*) AS total " . 
                                      "FROM " . self::$table_name . " " .
                                      "WHERE id = ? AND active = 1", 
                                      array(session_id()));

        return ($result->total > 0);
    }
}
