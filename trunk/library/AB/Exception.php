<?php

/**
 * Exception
 *
 * @category    Blotomate
 * @package     AB
 * @author      Rafael Castilho <rafael@castilho.biz>
 */
class AB_Exception extends Exception
{
    /**
     * Data
     *
     * @var array
     */
    protected $data;


    /**
     * Exception constructor
     *
     * @param   string  $message
     * @param   integer $code
     * @param   array   $data
     * @return void
     */
    public function __construct($message, 
                                $code=E_USER_NOTICE, 
                                $data=array())
    {
        /* force use of predefined codes */

        if(!in_array($code, array(E_USER_NOTICE,
                                  E_USER_WARNING,
                                  E_USER_ERROR)))
        {
            $code = E_USER_ERROR;
        }

        $this->data = $data;

        parent::__construct($message, $code);
    }

    /**
     * To string
     *
     * return string
     */
    public function __toString()
    {
        $message = null;
        $priority = null;

        switch($this->getCode())
        {
            case E_USER_ERROR:   $priority = "ERROR";   break;
            case E_USER_WARNING: $priority = "WARNING"; break;
            case E_USER_NOTICE:  $priority = "NOTICE";  break;
            default:             $priority = "ERROR";
        }

        $message = $priority . ": " . $this->getMessage();

        foreach($this->data as $name => $value)
        {
            $message.= ";\n" . $name . ": " . $value;
        }
            
        $message.= ";\nfile: " . $this->getFile();
        $message.= ";\nline: " . $this->getLine();

        if($priority == "ERROR")
        {
            $message.= ";\ntrace:\n" . $this->getTraceAsString();
        }

        return $message;
    }

    /**
     * Get overloading
     *
     * @param   string  $name
     * @return  mixed
     */
    public function __get($name)
    {
        $value = null;

        if(is_array($this->data))
        {
            if(array_key_exists($name, $this->data))
            {
                $value = $this->data[$name];
            }
        }

        return $value;
    }

    /**
     * Set overloading
     *
     * @param   string  $name
     * @param   mixed   $value
     * @return  void
     */
    public function __set($name, $value)
    {
        if(is_array($this->data) == false)
        {
            $this->data = array();
        }

        $this->data[$name] = $value;
    }

    /**
     * Write to log
     *
     * @param   string          $model      Model name
     * @return  void
     */
    public function log($model='ApplicationLog')
    {
        AB_Log::write($this->getMessage(), $this->getCode(), $this->data, $model);
    }

    /**
     * Forward exception
     *
     * @param   string          $message
     * @param   integer         $code
     * @param   Exception       $exception
     * @param   array           $data
     * @return  void
     */
    public static function forward($message, $code, $exception=null, $data=array())
    {
        if(is_object($exception))
        {
            if(get_class($exception) == __CLASS__)
            {
                $message.= ";\n" . $exception->getMessage();

                /* E_USER_ERROR < E_USER_WARNING < E_USER_NOTICE */

                if($exception->getCode() < $code)
                {
                    $code = $exception->getCode();
                }

                $data = array_merge($exception->data, $data);
            }
            else
            {
                $message.= ";\nexception: " . chop($exception->getMessage());
            }
        }

        throw new AB_Exception ($message, $code, $data);
    }
}
