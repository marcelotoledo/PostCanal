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
     * Configure controller
     */
    public function configure($action_name)
    {
        $this->hasView(false);
        $this->hasSession(false);
        $this->hasTranslation(false);
    }

    /**
     * Frontend
     */
    public function A_index()
    {
        new L_WebService($is_server=true);
    }
}
