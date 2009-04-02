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

        $client = new L_WebService();
        $results = $client->discover_feeds(array('url' => $url));

        if(count($results) > 0) $this->view->results = $results;
    }

    /**
     * Add feed
     *
     * @return void
     */
    public function A_add()
    {
        $this->response->setXML(true);

        $data = array("default", $this->request->url, "none none");
        $this->view->result = AggregatorFeed::procedureInsert($data);
    }
}
