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
        $results = $client->feed_discover(array('url' => $url));

        $this->session()->c_feed_discover_results = $results;
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

        $node = $this->request()->node;
        $blog_hash = $this->request()->blog;
        $feed_results = $this->session()->c_feed_discover_results;
        $user_profile_id = $this->session()->user_profile_id;

        $url = null;
        $url_len = 0;
        $title = null;

        if(array_key_exists($node, $feed_results))
        {
            $url = $feed_results[$node]['url'];
            $url_len = strlen($url);
            $title = $feed_results[$node]['title'];
        }

        $feed = null;

        /* check for existing feed */

        if($url_len > 0)
        {
            AggregatorFeed::transaction();
            $feed = AggregatorFeed::findByURL($url);
        }

        /* add new feed */

        if($url_len > 0 && is_object($feed) == false)
        {
            $feed = new AggregatorFeed();
            $feed->title = strlen($title) > 0 ? $title : "default";
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

        /* add feed to user blog feed */

        if(is_object($feed))
        {
            $blog_feed = UserBlogFeed::findByFeed($feed->aggregator_feed_id);

            if(is_object($blog_feed) == false)
            {
                $blog = UserBlog::findByHash($user_profile_id, $blog_hash);

                if(!is_object($blog))
                {
                    $_m = "invalid user blog from " .
                          "user_profile_id (" . $user_profile_id . ") and " .
                          "blog_hash (" . $blog_hash . ")";
                    $id = $user_profile_id;                          
                    $_d = array('method' => __METHOD__, 'user_profile_id' => $id);
                    throw new B_Exception($_m, E_USER_ERROR, $_d);
                }

                $blog_feed = new UserBlogFeed();
                $blog_feed->user_blog_id = $blog->user_blog_id;
                $blog_feed->aggregator_feed_id = $feed->aggregator_feed_id;
                $blog_feed->title = $feed->title;
                $blog_feed->save();
            }
        }

        $this->view()->result = is_object($feed) ? $feed->aggregator_feed_id : 0;
    }
}
