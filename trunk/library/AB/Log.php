<?php

/**
 * Log
 * 
 * @category    Autoblog
 * @package     AB
 */
class AB_Log
{
    /**
     * Log priority constants
     */
    const PRIORITY_INFO    = 0;
    const PRIORITY_WARNING = 1;
    const PRIORITY_ERROR   = 2;


    /**
     * Write log in database
     *
     * @param   string  $message    Log message
     * @param   integer $priority   Priority
     * @param   string  $model      Model name
     * @return  void
     */
    public static function write ($message, 
                                  $priority=self::PRIORITY_INFO, 
                                  $model='ApplicationLog')
    {
        if(class_exists($model) == false)
        {
            self::_write("class " . $model . " not found");
            echo "see error_log for more details\n";
        }
        else
        {
            $m = new $model();
            $m->message = $message;
            $m->priority = $priority;

            try
            {
                $m->save();
            }
            catch(Exception $exception)
            {
                $message = $exception->getMessage() . "; " . $message;
                self::writeErrorLog($message);
            }
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
