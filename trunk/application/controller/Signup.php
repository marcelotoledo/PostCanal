<?php

/**
 * Signup controller class
 * 
 * @category    PostCanal
 * @package     Application Controller
 * @author      Rafael Castilho <rafael@castilho.biz>
 */

class C_Signup extends B_Controller
{
    /**
     * Default action
     */
    public function A_index()
    {
        $this->view()->setLayout('index');
    }
}
