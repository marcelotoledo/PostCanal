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

        $blog_hash = $this->request()->blog;
        $user_profile_id = $this->session()->user_profile_id;
        $blog = null;

        if(strlen($blog_hash) > 0 && $user_profile_id > 0)
        {
            $blog = UserBlog::findByHash($user_profile_id, $blog_hash);
        }

        $feeds = array();

        if(is_object($blog))
        {
            $feeds = UserBlogFeed::findByBlog($blog->user_blog_id);
        }

        $results = array();

        foreach($feeds as $feed)
        {
            $results[] = array('feed' => $feed->hash,
                               'title' => $feed->title);
        }

        $this->view()->feeds = $results;
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

        $url = $this->request()->url;
        $results = array();

        /* check in aggregator feed discovery cache */

        $expiration = date("Y-m-d H:i:s", time() - mt_rand(43200, 86400));
        $discovery = AggregatorFeed::discover($url, null, $expiration);

        if(($discovery_len = count($discovery)) > 0)
        {
            for($i=0; $i<$discovery_len; $i++)
            {
                $results[] = array('title' => $discovery[$i]->title,
                                   'url' => $discovery[$i]->url_feed);
            }
        }

        /* discover feeds through webservice */

        else
        {
            $token = $this->registry->application->webservice->token;
            $client = new L_WebService();
            $results = $client->feed_discover(array('url' => $url));
            
            $results_len = count($results);

            for($i=0;$i<$results_len;$i++)
            {
                $data = array('url' => $url,
                              'url_feed' => $results[$i]['url'],
                              'title' => $results[$i]['title']);
                AggregatorFeed::register($data);
            }
        }

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

        $feed_key = intval($this->request()->key);
        $blog_hash = $this->request()->blog;
        $feed_results = $this->session()->c_feed_discover_results;
        $user_profile_id = $this->session()->user_profile_id;

        $url = null;
        $url_len = 0;
        $title = null;

        if(array_key_exists($feed_key, $feed_results))
        {
            $url = $feed_results[$feed_key]['url'];
            $url_len = strlen($url);
            $title = $feed_results[$feed_key]['title'];
        }

        $feed = null;

        /* check for existing feed */

        AggregatorFeed::transaction();

        if($url_len > 0)
        {
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
        }

        AggregatorFeed::commit();

        /* add feed to user blog feed */

        $blog_feed = null;

        if(is_object($feed))
        {
            $blog = UserBlog::findByHash($user_profile_id, $blog_hash);

            if(is_object($blog))
            {
                $blog_feed = UserBlogFeed::findByFeed($blog->user_blog_id, 
                                                      $feed->aggregator_feed_id);
            }
            else
            {
                $_m = "invalid user blog from " .
                      "user_profile_id (" . $user_profile_id . ") and " .
                      "blog_hash (" . $blog_hash . ")";
                $id = $user_profile_id;                          
                $_d = array('method' => __METHOD__, 'user_profile_id' => $id);
                throw new B_Exception($_m, E_USER_ERROR, $_d);
            }

            if(is_object($blog_feed) == false)
            {
                $blog_feed = new UserBlogFeed();
                $blog_feed->user_blog_id = $blog->user_blog_id;
                $blog_feed->aggregator_feed_id = $feed->aggregator_feed_id;
                $blog_feed->title = $feed->title;
                $blog_feed->save();
            }
        }

        $this->view()->feed = is_object($blog_feed) ? $blog_feed->hash : '';
    }
}
