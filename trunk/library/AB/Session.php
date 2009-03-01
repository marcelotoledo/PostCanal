<?php

/**
 * Session
 * 
 * @category    Blotomate
 * @package     AB
 * @author      Rafael Castilho <rafael@castilho.biz>
 */
class AB_Session
{
    /**
     * Session table name
     *
     * @var string
     */
    private static $table_name = 'application_session';


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
     * @param   integer     $id
     * @return  string
     */
    public static function read ($id)
    {
        $result = AB_Model::selectRow("SELECT session_data " . 
                                      "FROM " . self::$table_name . " " .
                                      "WHERE session = ?", array($id));
        return is_object($result) ? $result->session_data : '';
    }

    /**
     * Write the session
     *
     * @param   integer     $id
     * @param   string      $data
     * @return  boolean
     */
    public static function write($id, $data)
    {
        $result = AB_Model::selectRow("SELECT COUNT(*) AS total " . 
                                      "FROM " . self::$table_name . " " .
                                      "WHERE session = ?", array($id));

        return AB_Model::execute
        (
            ($result->total > 0) ? 
                "UPDATE " . self::$table_name . " SET " .
                "session_expires = ?, session_data = ? " .
                "WHERE session = ?" :
                "INSERT INTO " . self::$table_name . " VALUES (?, ?, ?)",
            ($result->total > 0) ?
                array(time(), $data, $id) :
                array($id, time(), $data)
        );
    }

    /**
     * Destroy the session
     *
     * @param   integer     $id
     * @return bool
     */
    public static function destroy ($id) 
    {
        return AB_Model::execute("DELETE FROM " . self::$table_name . " " .
                                 "WHERE session = ?",
                                 array($id));
    }

    /**
     * Garbage Collector
     *
     * @param   integer     $life 
     * @return  bool
     */
    public static function gc ($life) 
    {
        return AB_Model::execute("DELETE FROM " . self::$table_name . " " .
                                 "WHERE session_expires < ?",
                                 array(time() - $life));
    }

    /**
     * Session handler register
     *
     * @return  void
     */
    public static function register ()
    {
        ini_set('session.save_handler', 'user');

        session_set_save_handler(array('AB_Session', 'open'),
                                 array('AB_Session', 'close'),
                                 array('AB_Session', 'read'),
                                 array('AB_Session', 'write'),
                                 array('AB_Session', 'destroy'),
                                 array('AB_Session', 'gc')
        );
    }
}
