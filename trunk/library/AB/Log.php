<?php

/**
 * Log
 * 
 * @category    Blotomate
 * @package     AB
 * @author      Rafael Castilho <rafael@castilho.biz>
 */
class AB_Log
{
    /**
     * Log table name
     *
     * @var string
     */
    private static $table_name = 'ab_log';


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
        $columns = array('message', 'priority');
        $values = array($message, $priority);

        /* set extra data */

        foreach($data as $name => $value)
        {
            $columns[] = "data_" . $name;
            $values[] = $value;
        }

        try
        {
            AB_Model::execute("INSERT INTO " . self::$table_name . " " .
                              "(" . implode(", ", $columns) . ") VALUES " .
                              "(?" . str_repeat(", ?", count($columns) - 1) . ")",
                              $values);
        }
        catch(Exception $exception)
        {
            $message = $exception->getMessage() . "; " . $message;
            self::writeErrorLog($message);
        }
    }

    /**
     * Write log to application error_log file
     *
     * @param   string  $message    Log message
     * @return  void
     */
    private static function writeErrorLog ($message)
    {
        $message = preg_replace("/[\r\n]+/", "", $message);

        try
        {
            $f = fopen(BASE_PATH . "/log/error_log", "a");
            fwrite($f, $message . "\n");
            fclose($f);
        }
        catch(Exception $exception)
        {
            $message = $exception->getMessage() . "; " . $message;
            self::writeStdout($message);
        }
    }

    /**
     * Write log to stdout
     *
     * @param   string  $message    Log message
     * @return  void
     */
    private static function writeStdout ($message)
    {
        fwrite(STDOUT, $message);
    }
}
