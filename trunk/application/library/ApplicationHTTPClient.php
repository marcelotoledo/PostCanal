<?php

/**
 * Application HTTP Client
 * 
 * @category    Blotomate
 * @package     Application library
 * @author      Rafael Castilho <rafael@castilho.biz>
 */
class ApplicationHTTPClient
{
    /**
     * Status constants 
     */
    const STATUS_OK     = "status_ok";
    const STATUS_FAILED = "status_failed";
    const STATUS_3XX    = "status_3xx";
    const STATUS_4XX    = "status_4xx";
    const STATUS_5XX    = "status_5xx";


    /**
     * HTTP Client
     *
     * @var Zend_Http_Client
     */
    private $client;

    /**
     * Response
     *
     * @var Zend_Http_Response
     */
    private $response;


    /**
     * Application HTTP client constructor
     *
     * @return  void
     */
    public function __construct()
    {
        $this->client = new Zend_Http_Client();
    }

    /**
     * Request
     *
     * @param   string  $url
     * @return  boolean
     */
    public function request($url)
    {
        $this->response = null;
        $success = false;

        if(!empty($url))
        {
            try
            {
                $this->client->setUri($url);
                $this->response = $this->client->request();

                if(is_object($this->response))
                {
                    $success = true;
                }
            }
            catch(Exception $exception)
            {
                $message = "failed to get response from url (" . $url . ")";
                $data = array('method' => __METHOD__);
                AB_Exception::forward($message, E_USER_NOTICE, $exception, $data);
            }
        }

        return $success;
    }

    /**
     * Get response status
     *
     * @return  string
     */
    public function getStatus()
    {
        $status = 0;
        $result = self::STATUS_FAILED;

        if(is_object($this->response))
        {
            $status = $this->response->getStatus();
        }

        if($status >= 200 && $status < 300)
        {
            $result = self::STATUS_OK;
        }
        elseif($status >= 300 && $status < 400)
        {
            $result = self::STATUS_3XX;
        }
        elseif($status >= 400 && $status < 500)
        {
            $result = self::STATUS_4XX;
        }
        elseif($status >= 500 && $status < 600)
        {
            $result = self::STATUS_5XX;
        }

        return $result;
    }

    /**
     * Get headers
     *
     * @return  array
     */
    public function getHeaders()
    {
        $headers = array();

        if(is_object($this->response))
        {
            $headers = $this->response->getHeaders();
        }

        $total = count($headers);
        $registry = AB_Registry::singleton();
        $max_headers = $registry->http_client->max_headers;

        if(!empty($max_headers) && $total > $max_headers)
        {
            $headers = array_slice($headers, 0, $max_headers, true);

            $message = "the response has a total of " .
                       "(" . $total . ") headers " .
                       "and was reduced to " .
                       "(" . $max_headers . ") headers";
            $attributes = array('method' => __METHOD__);
            AB_Log::write($message, E_USER_WARNING, $attributes);
        }

        return $headers;
    }

    /**
     * Get Body
     *
     * @return  string 
     */
    public function getBody()
    {
        $body = "";

        if(is_object($this->response))
        {
            $body = $this->response->getBody();
            $lenght = strlen($body);
            $registry = AB_Registry::singleton();
            $max_body_lenght = $registry->http_client->max_body_lenght;

            if(!empty($max_body_lenght) && $lenght > $max_body_lenght)
            {
                $body = substr($body, 0, $max_body_lenght);

                $message = "the response body has a size of " .
                           "(" . $lenght . ") bytes " .
                           "and was truncated to " .
                           "(" . $max_body_lenght . ") bytes";
                $attributes = array('method' => __METHOD__);
                AB_Log::write($message, E_USER_WARNING, $attributes);
            }
        }

        return $body;
    }
}
