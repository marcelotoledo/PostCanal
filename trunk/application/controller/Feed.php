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

        if(count($blogs)==0)
        {
            header('Location: /site');
            exit(0);
        }
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
        $this->view()->tags = UserBlogFeed::findGroupByTags($blog_hash, $user_id);
    }

    /**
     * Discover feeds from URL
     */
    public function A_discover()
    {
        $this->response()->setXML(true);
        $this->view()->results = AggregatorFeed::discover($this->request()->url);
    }

    /**
     * Add feed
     */
    protected function feedAdd($blog_hash, $url, $title=null)
    {
        $user_id = $this->session()->user_profile_id;
        $result = null;

        if(is_object(($feed = AggregatorFeed::getByURL($url))))
        {
            $blog = UserBlog::getByUserAndHash($user_id, $blog_hash);
            $blog_feed = null;

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

            $result = array
            (
                'feed'       => $blog_feed->hash,
                'ordering'   => $blog_feed->ordering,
                'feed_url'   => $url,
                'feed_title' => $blog_feed->feed_title,
                'enabled'    => $blog_feed->enabled
            );
        }

        return $result;
    }

    protected function checkQuota()
    {
        $user_id = $this->session()->user_profile_id;
        $quota = $this->session()->user_profile_quota_feed;

        return ($quota > 0 && UserBlogFeed::total($user_id) >= $quota);
    }

    public function A_add()
    {
        $this->response()->setXML(true);

        $url = $this->request()->url;
        $blog = $this->request()->blog;
        $title = $this->request()->title;

        /* check quota */

        $oq = $this->checkQuota();
        $this->view()->overquota = $oq;
        if($oq) return false;

        /* add feed */

        $this->view()->feed = $this->feedAdd($blog, $url, $title);
    }

    public function A_quick()
    {
        $this->response()->setXML(true);

        $b = $this->request()->blog;
        $d = AggregatorFeed::discover($this->request()->url);
        if(array_key_exists(0, $d)==false) return false;
        $d = $d[0];
        $u = (is_array($d) && array_key_exists('feed_url', $d)) ? $d['feed_url'] : null;

        /* check quota */

        $oq = $this->checkQuota();
        $this->view()->overquota = $oq;
        if($oq) return false;

        /* add feed */

        if(strlen($b)==0 || strlen($u)==0) return false;
        $this->view()->feed = $this->feedAdd($b, $u);
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

        // tags (folders)
        if(strlen(($folders=$this->request()->folders))>0)
        {
            $folder_tags=L_Utility::splitTags($folders);
            $folder_tags_total=count($folder_tags);
            $blog_tags_assoc=UserBlogTag::findAssocFromUserBlog($user, $blog);
            $tag_ids=array();
            $blog_obj=null;

            for($j=0;$j<$folder_tags_total;$j++)
            {
                $tag_id=null;

                if((($tag_id=array_search($folder_tags[$j], $blog_tags_assoc))>0)==false)
                {
                    if($blog_obj==null) $blog_obj = UserBlog::getByUserAndHash($user, $blog);
                    if($blog_obj)
                    {
                        $o=new UserBlogTag();
                        $o->user_blog_id = $blog_obj->user_blog_id;
                        $o->name = $folder_tags[$j];
                        if($o->save()) $tag_id=$o->user_blog_tag_id;
                    }
                }

                if($tag_id>0) $tag_ids[]=$tag_id;
            }
        }

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
            UserBlogFeedTag::setTagIDArray($feed->user_blog_feed_id, $tag_ids);

            $updated = array_merge($updated, array('feed' => $hash));
        }
        $this->view()->updated = $updated;
    }
}
