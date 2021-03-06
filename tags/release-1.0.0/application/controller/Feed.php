<?php

/**
 * Feed controller class
 * 
 * @category    PostCanal
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
     * List feeds
     */
    public function A_index()
    {
        $this->view()->setLayout('dashboard');
        $id = $this->session()->user_profile_id;
        $blogs = UserBlog::findByUser($id, $enabled=true);
        $this->view()->blogs = $blogs;
        $this->view()->settings = UserDashboard::getByUser($id);
    }

    /**
     * List feeds
     */
    public function A_list()
    {
        $this->response()->setXML(true);
        $blog_hash = $this->request()->blog;
        $enabled = $this->request()->enabled ? true : false;
        $user_id = $this->session()->user_profile_id;
        $this->view()->feeds = UserBlogFeed::findAssocByBlogAndUser($blog_hash, 
                                                                    $user_id,
                                                                    $enabled);
    }

    /**
     * Discover feeds from URL
     */
    public function A_discover()
    {
        $this->response()->setXML(true);

        $user_id = $this->session()->user_profile_id;
        $quota = $this->session()->user_profile_quota_feed;

        if($quota > 0 && UserBlogFeed::total($user_id) >= $quota)
        {
            $this->view()->overquota = true;
            return false;
        }

        $this->view->results = AggregatorFeed::discover($this->request()->url);
    }

    /**
     * Add feed
     */
    protected function feedAdd($url, $title, $blog_hash)
    {
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
                throw new B_Exception($_m, E_WARNING, $_d);
            }

            if(is_object($blog_feed) == true)
            {
                $blog_feed->deleted = false;
                $blog_feed->setDefaultOrdering();
                $blog_feed->save();
            }
            else
            {
                $blog_feed = new UserBlogFeed();
                $blog_feed->user_blog_id = $blog->user_blog_id;
                $blog_feed->aggregator_feed_id = $feed->aggregator_feed_id;
                $blog_feed->feed_title = $title ? $title : $feed->feed_title;
                $blog_feed->feed_description = $feed->feed_description;
                $blog_feed->save();
            }
        }

        return $blog_feed;
    }

    public function A_add()
    {
        $this->response()->setXML(true);
        $url = $this->request()->url;
        $title = $this->request()->title;
        $blog_hash = $this->request()->blog;
        $blog_feed = $this->feedAdd($url, $title, $blog_hash);
        
        if(is_object($blog_feed))
        {
            $this->view()->feed = array
            (
                'feed'       => $blog_feed->hash,
                'ordering'   => $blog_feed->ordering,
                'feed_url'   => $url,
                'feed_title' => $blog_feed->feed_title,
                'enabled'    => $blog_feed->enabled
            );
        }
    }

    /**
     * Feed update ordering position
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

    /**
     * Feed toggle (enable/disable)
     */
    public function A_toggle()
    {
        $this->response()->setXML(true);
        $blog = $this->request()->blog;
        $feed = $this->request()->feed;
        $enable = $this->request()->enable;
        $user = $this->session()->user_profile_id;
        $this->view()->result = UserBlogFeed::updateColumn($user, $blog, $feed, 'enabled', $enable);
    }

    /**
     * Feed delete
     */
    public function A_delete()
    {
        $this->response()->setXML(true);
        $blog = $this->request()->blog;
        $feed = $this->request()->feed;
        $user = $this->session()->user_profile_id;
        $this->view()->result = UserBlogFeed::updateColumn($user, $blog, $feed, 'deleted', true);
    }

    /**
     * Feed Update
     */
    public function A_update()
    {
        $this->response()->setXML(true);
        $blog = $this->request()->blog;
        $hash = $this->request()->feed;
        $user = $this->session()->user_profile_id;
        $updated = array();

        if(is_object(($feed = UserBlogFeed::getByBlogAndFeedHash($user, $blog, $hash))))
        {
            foreach(UserBlogFeed::$allow_write as $k)
            {
                if(strlen($this->request()->{$k})>0)
                {
                    $feed->{$k} = $this->request()->{$k};
                    $updated = array_merge($updated, array($k => $feed->{$k}));
                }
            }
            $feed->save();
            $updated = array_merge($updated, array('feed' => $hash));
        }
        $this->view()->updated = $updated;
    }
}
