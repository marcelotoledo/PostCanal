<?php

/**
 * Queue controller class
 * 
 * @category    Blotomate
 * @package     Controller
 */
class C_Queue extends B_Controller
{
    /**
     * Before action
     */
    public function before()
    {
        $this->authorize();
    }

    /**
     * List queue items
     *
     * @return void
     */
    public function A_list()
    {
        $this->response()->setXML(true);
    }
}
