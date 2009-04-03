<?php

/**
 * Response
 * 
 * @category    Blotomate
 * @package     Base
 * @author      Rafael Castilho <rafael@castilho.biz>
 */
class B_Response
{
    /**
     * Response status codes
     * http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html
     */
    const STATUS_OK           = 200;
    const STATUS_REDIRECT     = 302;
    const STATUS_UNAUTHORIZED = 401;
    const STATUS_NOT_FOUND    = 404;
    const STATUS_ERROR        = 500;

    /**
     * Response headers
     *
     * @var array
     */
    private $headers = array();

    /**
     * Response status code
     *
     * @var integer
     */
    private $status = self::STATUS_OK;

    /**
     * Allow response redirect
     *
     * @var boolean
     */
    private $allow_redirect = true;

    /**
     * Response redirect
     *
     * @var boolean
     */
    private $is_redirect = false;

    /**
     * Response XML
     *
     * @var boolean
     */
    private $is_xml = false;

    /**
     * Response body
     *
     * @var string
     */
    private $body = "";

    
    /**
     * Response constructor
     *
     * @return  void
     */
    public function __construct()
    {
        $this->setContentType();
    }

    /**
     * Add item to header list
     *
     * @param   string  $label  Item label
     * @param   string  $value  Header content
     * @return  void
     */
    public function setHeader($label, $value)
    {
        $this->headers[$label] = $value;
    }

    /**
     * Remove item from header list
     *
     * @param   string  $label  Item label
     * @return  void
     */
    public function unsetHeader($label)
    {
        if(array_key_exists($label, $this->headers)) 
            unset($this->headers[$label]);
    }

    /**
     * Set response status code
     *
     * @param   integer $status Status code
     * @return  void
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * Get response status code
     *
     * @return  integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Allow redirect
     */
    public function allowRedirect($allow=true)
    {
        $this->allow_redirect = $allow;
    }

    /**
     * Set redirect
     *
     * @return  void
     */
    public function setRedirect($url, $status=self::STATUS_REDIRECT)
    {
        $this->setHeader('Location', $url);
        $this->status = $status;
        $this->is_redirect = true;
    }

    /**
     * Is redirect ?
     * 
     * @return  boolean
     */
    public function isRedirect()
    {
        return $this->is_redirect;
    }

    /**
     * Set XML
     *
     * @param   boolean     $is_xml
     * @return  void
     */
    public function setXML($is_xml)
    {
        $this->is_xml = $is_xml;
        $this->allow_redirect = $is_xml ^ true;
        $this->setContentType($is_xml ? 'text/xml' : 'text/html');
    }

    /**
     * Is XML
     *
     * @return  boolean
     */
    public function isXML()
    {
        return $this->is_xml;
    }

    /**
     * Set content type
     *
     * @return  void
     */
    public function setContentType($type='text/html', $charset='utf-8')
    {
        $this->setHeader('Content-Type', $type . "; charset=" . $charset);
    }

    /**
     * Set response body
     *
     * @param   string  $body
     * @return  void
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * Get response body
     *
     * @return  string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Send response [headers] and body
     *
     * @return  void
     */
    public function send()
    {
        if($this->status == self::STATUS_REDIRECT && 
           $this->allow_redirect === false)
        {
            unsetHeader('Location');
            $this->setBody(((string) null));
            $this->status = self::STATUS_ERROR;
        }

        $this->sendHeaders();
        $this->sendBody();
    }

    /**
     * Set headers from registry
     *
     * @param   integer     $status
     */
    private function setHeadersFromRegistry($status)
    {
        $registry = B_Registry::singleton();

        if(is_array($registry->response()->headers))
        {
            if(array_key_exists($status, $registry->response()->headers))
            {
                if(is_array($headers = $registry->response()->headers[$status]))
                {
                    foreach($headers as $name => $value)
                    {
                        $this->setHeader($name, $value);
                    }
                }
            }
        }
    }

    /**
     * Send response headers
     *
     * @return  void
     */
    private function sendHeaders()
    {
        $headers_file = null;
        $headers_line = null;
        $headers_sent = headers_sent($headers_file, $headers_line);

        if($headers_sent == false)
        {
            $this->setHeadersFromRegistry($this->status);

            header('HTTP/1.1 ' . $this->status);

            foreach($this->headers as $name => $header)
            {
                header($name . ": " . $header, true);
            }
        }
    }

    /**
     * Send response body
     *
     * @return  void
     */
    private function sendBody()
    {
        echo $this->body;
    }
}
