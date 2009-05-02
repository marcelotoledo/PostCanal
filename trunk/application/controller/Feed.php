<?php

/**
 * Feed controller class
 * 
 * @category    Blotomate
 * @package     Application Controller
 * @author      Rafael Castilho <rafael@castilho.biz>
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

        $this->view()->feeds = is_object($blog) ? 
            UserBlogFeed::findByBlog($blog->user_blog_id, $feed_id=null, true) :
            array();
    }

    /**
     * List feed news items
     *
     * @return void
     */
    public function A_news()
    {
        $this->response()->setXML(true);

        $id = $this->session()->user_profile_id;
        $blog_hash = $this->request()->blog;
        $feed_hash = $this->request()->feed;

        $blog = null;
        $feed = null;

        if(strlen($blog_hash) > 0)
        {
            $blog = UserBlog::findByHash($id, $blog_hash);
        }

        if(is_object($blog) && strlen($feed_hash) > 0)
        {
            $feed = UserBlogFeed::findByHash($blog->user_blog_id, $feed_hash);
        }

        if(is_object($feed) == false)
        {
            $_m = "user blog feed not found using " .
                  "blog hash (" . $blog_hash . ") and " .
                  "feed hash (" . $feed_hash . ")";
            $_d = array ('method' => __METHOD__, 'user_profile_id' => $id);
            throw new B_Exception($_m, E_USER_WARNING, $_d);
        }

        $news = AggregatorFeedItem::findByFeed(
            $feed->aggregator_feed_id, null, null, true);

        if(count($news) > 0) $this->view()->news = $news;
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
        $results = AggregatorFeed::discover($url);

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

        $url = $this->request()->url;
        $blog_hash = $this->request()->blog;
        $user_profile_id = $this->session()->user_profile_id;

        $blog_feed = null;

        if(is_object(($feed = AggregatorFeed::findByFeedURL($url))))
        {
            $blog = UserBlog::findByHash($user_profile_id, $blog_hash);

            if(is_object($blog))
            {
                $blog_feed = UserBlogFeed::findByFeed($blog->user_blog_id, 
                                                      $feed->aggregator_feed_id);
            }
            else
            {
                $_m = "invalid user blog from hash (" . $blog_hash . ")";
                $_i = $user_profile_id;                          
                $_d = array('method' => __METHOD__, 'user_profile_id' => $_i);
                throw new B_Exception($_m, E_USER_WARNING, $_d);
            }

            if(is_object($blog_feed) == false)
            {
                $blog_feed = new UserBlogFeed();
                $blog_feed->user_blog_id = $blog->user_blog_id;
                $blog_feed->aggregator_feed_id = $feed->aggregator_feed_id;
                $blog_feed->feed_title = $feed->feed_title;
                $blog_feed->feed_description = $feed->feed_description;
                $blog_feed->save();
            }
        }

        $this->view()->feed = is_object($blog_feed) ? $blog_feed->hash : '';
    }
}
