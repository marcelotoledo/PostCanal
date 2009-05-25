<?php

/**
 * Base Exception
 *
 * @category    PostCanal
 * @package     Base Library
 * @author      Rafael Castilho <rafael@castilho.biz>
 */

class B_Exception extends Exception
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
    public function __construct($message, $code=E_NOTICE, $data=array())
    {
        /* force use of predefined codes */

        if(!in_array($code, array(E_NOTICE, E_WARNING, E_ERROR)))
        {
            $code = E_ERROR;
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
            case E_ERROR:   $priority = "ERROR";   break;
            case E_WARNING: $priority = "WARNING"; break;
            case E_NOTICE:  $priority = "NOTICE";  break;
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
     * @return  void
     */
    public function writeLog()
    {
        B_Log::write($this->getMessage(), $this->getCode(), $this->data);
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

                /* E_ERROR < E_WARNING < E_NOTICE */

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

        throw new B_Exception ($message, $code, $data);
    }
}
