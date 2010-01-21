<?php

/**
 * Maintenance controller class
 * 
 * @category    PostCanal
 * @package     Application Controller
 */

class C_Maintenance extends B_Controller
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
     * Format article results
     */
    public function A_index()
    {
    }
}
