<?php

/**
 * Application Web Service Client/Server
 * 
 * @category    PostCanal
 * @package     Application Library
 * @author      Rafael Castilho <rafael@castilho.biz>
 */

class A_WebService
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
        $this->token = $registry->webservice()->token;

        if(($this->is_server = $is_server) === true)
        {
            $this->initializeServer(__CLASS__);
        }
        else
        {
            $url = BASE_URL . $registry->webservice()->backendUrl;
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

    // =========================================================================

    /* SERVER METHODS 
     *
     * VERY IMPORTANT: Do not use docblocks here
     * Zend_XmlRpc_Server use docblock to do introspection (UNECESSARY IN OUR CASE!)
     * http://framework.zend.com/manual/en/zend.xmlrpc.server.html#zend.xmlrpc.server.conventions
     *
     */

    /**
     * Validate WebService args (token, etc.)
     */
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
    public function feed_update_get($args)
    {
        if($this->validate_args($args, array()) == false) return null;
        return AggregatorFeed::findOutdated($limit=1);
    }

    /**
     * Post a updated feed
     */
    public function feed_update_post($args)
    {
        if($this->validate_args($args, array('id', 'data')) == false) return false;

        $feed = null;
        $updated = 0;

        try
        {
            $feed = AggregatorFeed::rawUpdate($args['id'], $args['data'], $updated);
        }
        catch(B_Exception $_e)
        {
            $_m = "feed update post webservice failed";
            $_d = array ('method' => __METHOD__);
            B_Log::write($_m, E_USER_ERROR, $_d);
        }

        return $updated;
    }

    /**
     * Get a blog entry awaiting publication
     */
    public function blog_publish_get($args)
    {
        if($this->validate_args($args, array()) == false) return null;
        return BlogEntry::findAwaitingPublication();
    }

    /**
     * Set a blog entry to published
     */
    public function blog_publish_set($args)
    {
        if($this->validate_args($args, array('id','published')) == false) return false;

        $entry = BlogEntry::getByPrimaryKey($args['id']);
        $published = ((boolean) $args['published']);

        try
        {
            $entry->publication_status = $published ? 
                BlogEntry::STATUS_PUBLISHED : 
                BlogEntry::STATUS_FAILED;
            $entry->save();
        }
        catch(B_Exception $_e)
        {
            $_m = "trying to set blog entry as published failed";
            $_d = array ('method' => __METHOD__);
            B_Log::write($_m, E_USER_ERROR, $_d);
        }
        
        return true;
    }

    /**
     * Do queue suggestion (blog entry feeding)
     */
    public function queue_feeding($args)
    {
        if($this->validate_args($args, array()) == false) return null;

        try
        {
            BlogEntry::feedingAuto();
        }
        catch(B_Exception $_e)
        {
            $_m = "trying to do queue feeding failed";
            $_d = array ('method' => __METHOD__);
            B_Log::write($_m, E_USER_ERROR, $_d);
        }
        
        return true;
    }
}
