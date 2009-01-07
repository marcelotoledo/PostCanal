<?php

/* AUTOBLOG RESPONSE */

class AB_Response
{
    private $headers = array();
    private $body = "";

    
    public function addHeader($header)
    {
        $this->headers[] = $header;
    }

    public function setBody($body)
    {
        $this->body = $body;
    }

    public function send()
    {
        for ($h=0; $h < count($this->headers); $h++)
        {
            header($h, true);
        }

        echo $this->body;
    }
}
