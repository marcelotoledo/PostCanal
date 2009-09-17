<?php

/**
 * Support controller class
 * 
 * @category    PostCanal
 * @package     Application Controller
 * @author      Rafael Castilho <rafael@castilho.biz>
 */

class C_Support extends B_Controller
{
    public function before()
    {
        $this->view()->setLayout('index');
    }

    /**
     * Default action
     */
    public function A_index()
    {
    }
}
