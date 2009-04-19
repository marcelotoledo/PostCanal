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
     * Web Service is server?
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
            $this->initializeServer(__CLASS__);
        }
        else
        {
            $url = BASE_URL . $registry->application()->webservice()->backend()->url;
            $this->initializeClient($url);
        }

        $this->throw_exception = $throw_exception;
    }

    /**
     * Call Web Service Method (Client only)
     */
    public function __call($method, $args)
    {
        $args = array_merge(array('token' => $this->token), current($args));
        $results = null;

        try
        {
            $results = $this->xmlrpc->call($method, array($args));
        }
        catch(Exception $e)
        {
            $_m = "failed to call webservice method (" . $method . ") as client, " .
                  "with args [" . implode(", ", ((array) $args)) . "];\n" .
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

        return $results;
    }

    /**
     * Web Service Server
     */
    protected function initializeServer()
    {
        $this->xmlrpc = new Zend_XmlRpc_Server();
        $this->xmlrpc->setClass(__CLASS__);
        echo $this->xmlrpc->handle();
        exit(0); // avoid junk
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

    /* SERVER METHODS */

    protected function validate_args($args, $names=array())
    {
        $token_ok = (array_key_exists('token', $args) && $args['token'] == $this->token);
        $keys_ok = true;

        foreach($names as $name)
        {
            if(!array_key_exists($name, $args))
            {
                $keys_ok = false;
            }
        }

        return $token_ok && $keys_ok;
    }

    /**
     * Get a feed that needs update
     */
    public function feed_update($args)
    {
        if($this->validate_args($args, array()) == false) return null;

        $feed = AggregatorFeed::findNeedUpdate();
        $result = array();

        if(is_object($feed))
        {
            $result = array('url'      => $feed->feed_url,
                            'modified' => $feed->feed_modified);
        }

        return $result;
    }
}
