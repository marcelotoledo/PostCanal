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

        if(strlen($this->consumer_key)==0 || strlen($this->consumer_secret)==0)
        {
            return $token;
        }

        $result = null;

        if($this->blog_type==self::BLOG_TYPE_TWITTER)
        {
            $result = self::getRequestTokenTwitter($this->consumer_key,
                                                   $this->consumer_secret);
        }

        if(is_array($result) &&
           array_key_exists('oauth_token', $result) &&
           array_key_exists('oauth_token_secret', $result))
        {
            $token = $result;
        }

        return $token;
    }

    public function getAccessToken($oauth_token, $oauth_token_secret)
    {
        $token = array('oauth_token'        => '',
                       'oauth_token_secret' => '');

        if(strlen($this->consumer_key)==0 || strlen($this->consumer_secret)==0)
        {
            return $token;
        }

        $result = null;

        if($this->blog_type==self::BLOG_TYPE_TWITTER)
        {
            $result = self::getAccessTokenTwitter($this->consumer_key,
                                                  $this->consumer_secret,
                                                  $oauth_token,
                                                  $oauth_token_secret);
        }

        if(is_array($result) &&
           array_key_exists('oauth_token', $result) &&
           array_key_exists('oauth_token_secret', $result))
        {
            $token = $result;
        }

        return $token;
    }

    protected static function getRequestTokenTwitter($k, $s)
    {
        require_once 'twitteroauth/twitteroauth.php';
        $c = new TwitterOAuth($k, $s);
        return $c->getRequestToken();
    }

    protected static function getAccessTokenTwitter($k, $s, $tk, $ts)
    {
        require_once 'twitteroauth/twitteroauth.php';
        $c = new TwitterOAuth($k, $s, $tk, $ts);
        return $c->getAccessToken();
    }
}
