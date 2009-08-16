<?php

/**
 * Contact controller class
 * 
 * @category    PostCanal
 * @package     Application Controller
 * @author      Rafael Castilho <rafael@castilho.biz>
 */

class C_Contact extends B_Controller
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
