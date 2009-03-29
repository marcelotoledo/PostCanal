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

    /**
     * Discover feeds from URL
     *
     * @return void
     */
    public function A_discover()
    {
        $this->response->setXML(true);

        $token = $this->registry->application->webservice->token;
        $url = $this->request->url;

        $client = new Zend_XmlRpc_Client(B_Request::url('webservice','backend'));
        $args = array('token' => $token, 'url' => $url);
        $result = $client->call('discover_feed', array($args));

        $this->view->discovery = $result;
    }

    /**
     * Add feed
     *
     * @return void
     */
    public function A_add()
    {
        $this->response->setXML(true);
    }
}
