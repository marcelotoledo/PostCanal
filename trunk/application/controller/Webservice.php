<?php

/**
 * Webservice controller class
 * 
 * @category    Blotomate
 * @package     Controller
 */
class C_Webservice extends B_Controller
{
    /**
     * Before action
     */
    public function before()
    {
        $this->view()->setLayout(null);
        $this->view()->setTemplate(null);
    }

    /**
     * Frontend
     */
    public function A_frontend()
    {
        new L_WebService($is_server=true);
    }
}
