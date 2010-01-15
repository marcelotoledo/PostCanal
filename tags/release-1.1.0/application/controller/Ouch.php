<?php

/**
 * Ouch controller class
 * 
 * @category    PostCanal
 * @package     Application Controller
 */

class C_Ouch extends B_Controller
{
    /**
     * Default action
     *
     * @return void
     */
    public function A_index()
    {
        $this->view()->setLayout('default');
        $this->view()->message = B_Bootstrap::error('ouch');
    }
}
