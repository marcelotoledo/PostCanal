<?php

/**
 * Exception
 *
 * @category    Blotomate
 * @package     AB
 */
class AB_Exception extends Exception
{
    /**
     * Controller
     * 
     * @var string
     */
    protected $controller;
     
    /**
     * Action
     * 
     * @var string
     */
    protected $action;
 

    /**
     * Exception constructor
     *
     * @param   string  $message
     * @param   integer $code
     * @param   string  $controller
     * @param   string  $action
     * @return void
     */
    public function __construct($message, 
                                $code=E_USER_NOTICE, 
                                $controller=null, 
                                $action=null)
    {
        /* force use of predefined codes */

        if(!in_array($code, array(E_USER_NOTICE,
                                  E_USER_WARNING,
                                  E_USER_ERROR)))
        {
            $code = E_USER_ERROR;
        }

        $this->controller = $controller;
        $this->action = $action;

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
            default:             $priority = "UNKNOWN"; /* must never occur */
        }

        $message = $priority . ": " . $this->getMessage();

        if(!empty($this->controller))
            $message.= "; controller: " . $this->controller;

        if(!empty($this->action))
            $message.= "; action: " . $this->action;

        $message.= "; file: " . $this->getFile();
        $message.= "; line: " . $this->getLine();
        $message.= "; trace: " . $this->getTraceAsString();

        return $message;
    }

    /**
     * Get controller
     *
     * @return string
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Set controller
     *
     * @param   string  $controller
     * @return  void
     */
    public function setController($controller)
    {
        $this->controller = $controller;
    }

    /**
     * Get action
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set action
     *
     * @param   string  $action
     * @return  void
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * Throw new exception
     *
     * @param   string          $message
     * @param   integer         $code
     * @param   Exception       $exception
     * @return  void
     */
    public static function throwNew($message, $code, $exception=null)
    {
        if(is_object($exception))
        {
            $message.= ";\n" . $exception->getMessage();

            if(get_class($exception) == __CLASS__)
            {
                /* E_USER_ERROR < E_USER_WARNING < E_USER_NOTICE */

                if($exception->getCode() < $code)
                {
                    $code = $exception->getCode();
                }
            }
        }

        throw new AB_Exception ($message, $code);
    }
}
