<?php

/**
 * Webservice controller class
 * 
 * @category    PostCanal
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
        new L_WebService($is_server=true);
    }
}
