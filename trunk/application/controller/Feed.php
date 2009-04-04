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
        $this->view()->setLayout('dashboard');
    }

    /**
     * List feeds
     *
     * @return void
     */
    public function A_list()
    {
        $this->response()->setXML(true);
    }

    /**
     * List feed items
     *
     * @return void
     */
    public function A_item()
    {
        $this->response()->setXML(true);
    }

    /**
     * Discover feeds from URL
     *
     * @return void
     */
    public function A_discover()
    {
        $this->response()->setXML(true);

        $token = $this->registry->application->webservice->token;
        $url = $this->request()->url;

        $client = new L_WebService();
        $results = $client->discover_feeds(array('url' => $url));

        if(count($results) > 0) $this->view()->results = $results;
    }

    /**
     * Add feed
     *
     * @return void
     */
    public function A_add()
    {
        $this->response()->setXML(true);

        $feed = null;
        $url = $this->request()->url;
        $url_len = strlen($url);

        /* check for existing feed */

        if($url_len > 0)
        {
            AggregatorFeed::transaction();
            $feed = AggregatorFeed::findByURL($url);
        }

        /* add new feed */

        if($url_len > 0 && $feed == null)
        {
            $feed = new AggregatorFeed();
            $feed->title = "default " . time();
            $feed->url = $url;
            $feed->url_md5 = md5($url);

            try
            {
                $feed->save();
            }
            catch(B_Exception $exception)
            {
                AggregatorFeed::rollback();
                $_m = "aggregator feed transaction failed";
                $_d = array ('method' => __METHOD__);
                B_Exception::forward($_m, E_USER_ERROR, $exception, $_d);
            }

            AggregatorFeed::commit();
        }

        $this->view()->result = is_object($feed) ? $feed->aggregator_feed_id : 0;
    }
}
