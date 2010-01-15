<?php

/**
 * Index controller class
 * 
 * @category    PostCanal
 * @package     Application Controller
 * @author      Rafael Castilho <rafael@castilho.biz>
 */

class C_Index extends B_Controller
{
    public function before()
    {
        $this->view()->setLayout('index');

        if(B_Registry::get('application/maintenance')=='true')
        {
            $this->response()->setRedirect(B_Request::url('maintenance'));
        }
    }

    /**
     * Default action
     */
    public function A_index()
    {
        if($this->session()->getActive() == true)
        {
            $this->response()->setRedirect(B_Request::url('rw'));
        }
    }
}
