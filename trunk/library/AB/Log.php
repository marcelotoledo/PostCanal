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
     * Write log
     *
     * @param   string  $message    Log message
     * @param   integer $priority   Priority
     * @param   array   $attributes Extra attributes
     * @param   string  $model      Model name
     * @return  void
     */
    public static function write ($message,
                                  $priority=E_USER_NOTICE,
                                  $attributes=array(),
                                  $model='ApplicationLog')
    {
        if(class_exists($model) == false)
        {
            self::writeErrorLog("model (" . $model . ") not found");
        }
        else
        {
            $m = new $model();
            $m->message = $message;
            $m->priority = $priority;

            /* set extra attributes */

            foreach($attributes as $name => $value)
            {
                $m->{$name} = $value;
            }
            
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
