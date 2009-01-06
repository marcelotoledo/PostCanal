<?php

/* AUTOBLOG RESPONSE */

class AB_Response
{
    public $headers = array();
    public $body = "";


    public function send()
    {
        for ($h = 0; $h < count($this->headers); $h++)
        {
            header($h, true);
        }

        echo $this->body;
    }
}
