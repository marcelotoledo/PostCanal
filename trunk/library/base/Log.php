<?php

/**
 * Base Log
 * 
 * @category    Blotomate
 * @package     Base Library
 * @author      Rafael Castilho <rafael@castilho.biz>
 */

class B_Log
{
    /**
     * Log table name
     *
     * @var string
     */
    private static $table_name = 'base_log';


    /**
     * Write log
     *
     * @param   string  $message    Log message
     * @param   integer $priority   Priority
     * @param   array   $data       Data (data_* columns)
     * @return  void
     */
    public static function write ($message,
                                  $priority=E_USER_NOTICE,
                                  $data=array())
    {
        $columns = array('message', 'priority', 'created_at');
        $values = array($message, $priority, date('Y-m-d H:i:s'));

        /* set extra data */

        foreach($data as $name => $value)
        {
            $columns[] = "data_" . $name;
            $values[] = $value;
        }

        try
        {
            B_Model::execute("INSERT INTO " . self::$table_name . " " .
                              "(" . implode(", ", $columns) . ") VALUES " .
                              "(?" . str_repeat(", ?", count($columns) - 1) . ")",
                              $values);
        }
        catch(Exception $exception)
        {
            $message = chop($exception->getMessage()) . "; " . chop($message);
            self::systemLog($message);
        }
    }

    /**
     * Write log to system log
     *
     * @param   string  $message    Log message
     */
    public static function systemLog ($message)
    {
        // echo syslog(LOG_ERR, $message) ?
        //     "fatal error: please see syslog for details" :
        //     $message;
        echo $message;
        exit(1);
    }
}
