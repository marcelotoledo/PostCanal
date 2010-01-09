<?php

/**
 * OAuth wraper
 * 
 * @category    PostCanal
 * @package     Application Library
 * @author      Rafael Castilho <rafael@castilho.biz>
 */

require_once('oauth/OAuth.php');


class L_OAuthWrapper
{
    private $blog_type;
    private $consumer_key;
    private $consumer_secret;
    private $callback_url;


    const BLOG_TYPE_TWITTER = 'twitter';


    public function __construct($type, 
                                $consumer_key, 
                                $consumer_secret, 
                                $callback_url=null)
    {
        $this->blog_type       = $type;
        $this->consumer_key    = $consumer_key;
        $this->consumer_secret = $consumer_secret;
        $this->callback_url    = $callback_url;
    }

    public function getRequestToken()
    {
        $token = array('oauth_token'        => '',
                       'oauth_token_secret' => '');

        if($this->blog_type==self::BLOG_TYPE_TWITTER && 
           strlen($this->consumer_key)>0 &&
           strlen($this->consumer_secret)>0)
        {
            $token = self::getRequestTokenTwitter($this->consumer_key,
                                                  $this->consumer_secret);
        }

        return $token;
    }

    protected static function getRequestTokenTwitter($k, $s)
    {
        require_once 'twitteroauth/twitteroauth.php';
        $c = new TwitterOAuth($k, $s);
        return $c->getRequestToken();
    }
}
