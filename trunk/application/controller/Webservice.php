<?php

/**
 * Webservice controller class
 * 
 * @category    Blotomate
 * @package     Application Controller
 * @author      Rafael Castilho <rafael@castilho.biz>
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
    public function A_index()
    {
        new A_WebService($is_server=true);
    }
}
