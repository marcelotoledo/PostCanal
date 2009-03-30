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
        $method = 'discover_feed';
        $args = array('token' => $token, 'url' => $url);
        $results = "";

        try
        {
            $results = $client->call($method, array($args));
        }
        catch(Exception $e)
        {
            $_m = "failed to call webservice method (" . $method . ") " .
                  "using token (" . $token . ") " .
                  "and url (" . $url . ");\n" . 
                  "exception (" . $e->getMessage() . ")";
            $_d = array('method' => __METHOD__);
            B_Log::write($_m, E_USER_WARNING, $_d);
        }

        $discovery = array();

        foreach(explode(';', $results) as $i => $r)
        {
            if(strlen($r) > 0) $discovery['feed_' . $i] = $r;
        }

        $this->view->discovery = $discovery;
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
