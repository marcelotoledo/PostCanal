<?php

/**
 * Application HTTP Client
 * 
 * @category    Blotomate
 * @package     Application library
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
     * Overflow constants
     */
    const MAX_HEADERS     = 256;
    const MAX_BODY_LENGHT = 5242880; // 5242880 bytes = 5Mb


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
                AB_Exception::throwNew($message, E_USER_NOTICE, $exception);
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

        if($total > self::MAX_HEADERS)
        {
            $headers = array_slice($headers, 0, self::MAX_HEADERS, true);

            $message = "the response has a total of " . 
                       "(" . $total . ") headers " .
                       "and was reduced to ";
                       "(" . self::MAX_HEADERS . ") headers";

            AB_Log::write($message, E_USER_WARNING);
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

            if($lenght > self::MAX_BODY_LENGHT)
            {
                $body = substr($body, 0, self::MAX_BODY_LENGHT);

                $message = "the response body has a size of " . 
                           "(" . $lenght . ") bytes " .
                           "and was truncated to ";
                           "(" . self::MAX_BODY_LENGHT . ") bytes";

                AB_Log::write($message, E_USER_WARNING);
            }
        }

        return $body;
    }

    /**
     * Fix URL
     * 
     * @param   string      $url
     * @return  string
     */
    public static function fixURL($url)
    {
        $pattern = "#^(.*?//)*([\w\.\d]*)(:(\d+))*(/*)(.*)$#";
        $matches = array();
        preg_match($pattern, $url, $matches);

        $protocol = empty($matches[1]) ? "http://" : $matches[1];
        $address  = empty($matches[2]) ? ""        : $matches[2];
        $port     = empty($matches[3]) ? ""        : $matches[3];
        $resource = empty($matches[6]) ? ""        : $matches[5] . $matches[6];

        return $protocol . $address . $port . $resource;
    }
}
