<?php

/**
 * Application HTTP Client
 * 
 * @category    PostCanal
 * @package     Application Library
 * @author      Rafael Castilho <rafael@castilho.biz>
 */

class L_HTTPClient
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
                B_Exception::forward($message, E_NOTICE, $exception, $data);
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

        $max = intval(B_Registry::get('httpClient/maxHeaders'));
        $total = count($headers);

        if($max > 0 && $total > $max)
        {
            $headers = array_slice($headers, 0, $max, true);

            $_m= "the response has a total of (" . $total . ") headers " .
                 "and was reduced to (" . $max . ") headers";
            $_d = array('method' => __METHOD__);
            B_Log::write($_m, E_WARNING, $_d);
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
            $max = intval(B_Registry::get('httpClient/maxBodyLenght'));
            $lenght = strlen($body);

            if($max > 0 && $lenght > $max)
            {
                $body = substr($body, 0, $max);
                $_m = "the response body has a size of (" . $lenght . ") bytes " .
                      "and was truncated to (" . $max . ") bytes";
                $_d = array('method' => __METHOD__);
                B_Log::write($_m, E_WARNING, $_d);
            }
        }

        return $body;
    }
}
