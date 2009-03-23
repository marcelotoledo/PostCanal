<?php

/**
 * Feed controller class
 * 
 * @category    Blotomate
 * @package     Controller
 */
class C_Feed extends B_Controller
{
    /**
     * Before action
     */
    public function before()
    {
        $this->authorize();
    }

    /**
     * Default action
     *
     * @return void
     */
    public function A_index()
    {
        $this->view->setLayout('dashboard');
    }

    /**
     * List feeds
     *
     * @return void
     */
    public function A_list()
    {
        $this->response->setXML(true);
    }

    /**
     * List feed items
     *
     * @return void
     */
    public function A_item()
    {
        $this->response->setXML(true);
    }
}
