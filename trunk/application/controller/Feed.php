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

        $id = $this->session()->user_profile_id;
        $blogs = UserBlog::findByUser($id, $enabled=true);
        $this->view()->blogs = $blogs;
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
        $user_id = $this->session()->user_profile_id;

        $this->session()->user_blog_hash = $blog_hash;
        $this->view()->feeds = UserBlogFeed::partialByBlogAndUser($blog_hash, $user_id);
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
            $blog = UserBlog::getByUserAndHash($id, $blog_hash);
        }

        if(is_object($blog) && strlen($feed_hash) > 0)
        {
            $feed = UserBlogFeed::getByUserAndHash($blog->user_blog_id, $feed_hash);
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
        $user_id = $this->session()->user_profile_id;

        $blog_feed = null;

        if(is_object(($feed = AggregatorFeed::getByURL($url))))
        {
            $blog = UserBlog::getByUserAndHash($user_id, $blog_hash);

            if(is_object($blog))
            {
                $blog_feed = UserBlogFeed::getByBlogAndFeed(
                    $blog->user_blog_id, 
                    $feed->aggregator_feed_id);
            }
            else
            {
                $_m = "invalid user blog from hash (" . $blog_hash . ")";
                $_d = array('method' => __METHOD__, 'user_profile_id' => $user_id);
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

    /**
     * Update feed position
     *
     * @return void
     */
    public function A_position()
    {
        $this->response()->setXML(true);

        $blog = $this->request()->blog;
        $feed = $this->request()->feed;
        $position = $this->request()->position;
        $user_id = $this->session()->user_profile_id;

        UserBlogFeed::updateOrdering($blog, $user_id, $feed, $position);

        $this->view()->updated = true;
    }
}
