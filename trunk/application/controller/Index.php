<?php

/**
 * Index controller class
 * 
 * @category    Blotomate
 * @package     Application Controller
 * @author      Rafael Castilho <rafael@castilho.biz>
 */

class C_Index extends B_Controller
{
    /**
     * Default action
     */
    public function A_index()
    {
        if($this->session()->getActive() == true)
        {
            $this->response()->setRedirect(B_Request::url("dashboard"));
        }
    }
}
