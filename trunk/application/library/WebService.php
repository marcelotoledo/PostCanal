<?php

/**
 * Application Web Service Client/Server
 * 
 * @category    PostCanal
 * @package     Application Library
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
        $cfg= B_Registry::get('webservice');
        $this->token = $cfg->token;

        if(($this->is_server = $is_server) === true)
        {
            $this->initializeServer(__CLASS__);
        }
        else
        {
            $url = BASE_URL . $cfg->backendUrl;
            $this->initializeClient($url);
        }

        $this->throw_exception = $throw_exception;
    }

    /**
     * Call Web Service Method (Client)
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
                throw new B_Exception($_m, E_WARNING, $_d);
            }
            else
            {
                B_Log::write($_m, E_WARNING, $_d);
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
        $total = array_key_exists('total', $args) ? intval($args['total']) : 1;
        return AggregatorFeed::findOutdated($total);
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
            B_Log::write($_m, E_ERROR, $_d);
        }

        return $updated;
    }

    /**
     * Reset feed update time for all feeds
     */
    public function feed_update_reset($args)
    {
        if($this->validate_args($args, array()) == false) return false;
        AggregatorFeed::resetUpdate();
    }

    /**
     * Feed update total items
     */
    public function feed_update_total($args)
    {
        if($this->validate_args($args, array()) == false) return false;
        return AggregatorFeed::getOutdatedTotal();
    }

    /**
     * Get a blog entry awaiting publication
     */
    public function blog_publish_get($args)
    {
        if($this->validate_args($args, array()) == false) return null;
        $total = array_key_exists('total', $args) ? intval($args['total']) : 1;
        return BlogEntry::findAwaitingPublication($total);
    }

    /**
     * Set a blog entry to published
     */
    public function blog_publish_set($args)
    {
        if($this->validate_args($args, array('id','published')) == false) return false;

        $status = null;

        if(array_key_exists('published', $args))
        {
            $status = ((boolean) $args['published']);
        }
        if(array_key_exists('status', $args))
        {
            $status = $args['status'];
        }

        try
        {
            BlogEntry::setPublicationStatus($args['id'], $status);
        }
        catch(B_Exception $_e)
        {
            $_m = "trying to set blog entry as published failed";
            $_d = array ('method' => __METHOD__);
            B_Log::write($_m, E_ERROR, $_d);
        }
        
        return true;
    }

    /**
     * Reset blog entry lock for all entries
     */
    public function blog_publish_reset($args)
    {
        if($this->validate_args($args, array()) == false) return false;
        BlogEntry::releaseWorking();
    }

    /**
     * Blog publish total items
     */
    public function blog_publish_total($args)
    {
        if($this->validate_args($args, array()) == false) return false;
        return BlogEntry::getAwaitingPublicationTotal();
    }

    /**
     * Do queue suggestion (blog entry feeding)
     */
    public function queue_suggest_do($args)
    {
        if($this->validate_args($args, array()) == false) return null;

        $blog_id = 0;

        try
        {
            $blog_id = BlogEntry::suggestEntry();
        }
        catch(B_Exception $_e)
        {
            $_m = "queue suggest failed";
            $_d = array ('method' => __METHOD__);
            B_Log::write($_m, E_ERROR, $_d);
        }
        
        return $blog_id;
    }

    /**
     * Reset queue suggestion
     */
    public function queue_suggest_reset($args)
    {
        if($this->validate_args($args, array()) == false) return false;
        UserBlog::resetSuggest();
    }
}
