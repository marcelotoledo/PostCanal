<?php

/**
 * Index controller class
 * 
 * @category    Blotomate
 * @package     Controller
 * @author      Rafael Castilho <rafael@castilho.biz>
 */
class C_Index extends C_Abstract
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
