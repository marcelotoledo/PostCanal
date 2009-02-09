<?php

/**
 * Log
 * 
 * @category    Blotomate
 * @package     AB
 */
class AB_Log
{
    /**
     * Write log
     *
     * @param   string  $message    Log message
     * @param   integer $priority   Priority
     * @param   string  $model      Model name
     * @return  void
     */
    public static function write ($message,
                                  $priority=E_USER_NOTICE,
                                  $controller=null,
                                  $action=null,
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
            
            if(!empty($controller)) $m->controller = $controller;
            if(!empty($action)) $m->action = $action;

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
     * Write AB_Exception log
     *
     * @param   AB_Exception    $exception  Exception
     * @param   string          $model      Model name
     * @return  void
     */
    public static function writeException (
        AB_Exception $exception, 
        $model='ApplicationLog')
    {
        self::write($exception->getMessage(),
                    $exception->getCode(),
                    $exception->getController(),
                    $exception->getAction(),
                    $model);
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
