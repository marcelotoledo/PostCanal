<?php

/**
 * Plans controller class
 * 
 * @category    PostCanal
 * @package     Application Controller
 * @author      Rafael Castilho <rafael@castilho.biz>
 */

class C_Plans extends B_Controller
{
    /* configure controller */

    public function configure($action_name)
    {
        $this->hasSession(false);
    }

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
