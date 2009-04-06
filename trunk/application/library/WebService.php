<?php

/**
 * Application Web Service Client/Server
 * 
 * @category    Blotomate
 * @package     Library
 * @author      Rafael Castilho <rafael@castilho.biz>
 */
class L_WebService
{
    /**
     * Web Service token
     *
     * @var string
     */
    private $token;

    /**
     * Web Service is Server?
     *
     * @var boolean
     */
    private $is_server;

    /**
     * Throw Web Service Exception?
     *
     * @var boolean
     */
    private $throw_exception;

    /**
     * Web Service client
     *
     * @var Zend_XmlRpc_Client|Zend_XmlRpc_Server
     */
    private $xmlrpc;


    /**
     * Constructor
     */
    public function __construct($is_server=false, $throw_exception=false)
    {
        $registry = B_Registry::singleton();
        $this->token = $registry->application()->webservice()->token;

        if(($this->is_server = $is_server) === true)
        {
            $this->initializeServer();
        }
        else
        {
            $url = BASE_URL . $registry->application->webservice->backend->url;
            $this->initializeClient($url);
        }

        $this->throw_exception = $throw_exception;
    }

    /**
     * Call Web Service Method
     */
    public function __call($method, $args)
    {
        $args = array_merge(array('token' => $this->token), current($args));
        $results = array();

        if($this->is_server)
        {
            /* TODO */
        }
        else
        {
            try
            {
                $results = $this->xmlrpc->call($method, array($args));
            }
            catch(Exception $e)
            {
                $_m = "failed to call webservice method (" . $method . ") " .
                      "with args [" . implode(", ", ((array) $args)) . "] " .
                      "as client, using token (" . $this->token . ");\n" .
                      "exception: " . $e->getMessage();
                $_d = array('method' => __METHOD__);

                if($this->throw_exception)
                {
                    throw new B_Exception($_m, E_USER_WARNING, $_d);
                }
                else
                {
                    B_Log::write($_m, E_USER_WARNING, $_d);
                }
            }
        }

        return $results;
    }

    /**
     * Web Service Server (TODO)
     */
    protected function initializeServer()
    {
        /* void */
    }

    /**
     * Web Service Client
     *
     * @param   string  $url    Web Service Server URL
     */
    protected function initializeClient($url)
    {
        $this->xmlrpc = new Zend_XmlRpc_Client($url);
    }
}
