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
    protected function feedAdd($blog_hash, $url, $title=null, $link=null, $add=false)
    {
        $user_id = $this->session()->user_profile_id;
        $blog_feed = null;
        $feed = null;

        if(is_object(($feed = AggregatorFeed::getByURL($url)))==false && $add)
        {
            $feed = new AggregatorFeed();
            $feed->feed_url = $url;
            $feed->feed_url_md5 = md5($url);
            $feed->feed_title = '';
            $feed->feed_description = '';
            $feed->feed_link = $link;
            $feed->feed_update_time = 0;
            $feed->feed_status = 404;
            $feed->save();
        }

        if(is_object($feed))
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

    protected function feedDiscoverAndAdd($query, $blog)
    {
        $d = AggregatorFeed::discover($query);
        if(array_key_exists(0, $d)==false) return false;
        $d = $d[0];
        $u = (is_array($d) && array_key_exists('feed_url', $d)) ? $d['feed_url'] : null;
        if(strlen($u)==0) return false;
        return $this->feedAdd($blog, $u);
    }

    protected function formatFeedAdd($blog_feed, $url)
    {
        if(is_object($blog_feed)==false) return null;
        return array
        (
            'feed'       => $blog_feed->hash,
            'ordering'   => $blog_feed->ordering,
            'feed_url'   => $url,
            'feed_title' => $blog_feed->feed_title,
            'enabled'    => $blog_feed->enabled
        );
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

        $this->view()->feed = $this->formatFeedAdd($this->feedAdd($blog, $url, $title), $url);
    }

    public function A_quick()
    {
        $this->response()->setXML(true);

        $user  = $this->session()->user_profile_id;
        $blog  = $this->request()->blog;
        $query = $this->request()->url;

        if(strlen($blog)==0 || strlen($query)==0) return false;

        /* check quota */

        $oq = $this->checkQuota();
        $this->view()->overquota = $oq;
        if($oq) return false;

        /* add keyword */

        $url = null;

        if(strpos($query, '://')>0)
        {
            $url = $query;
        }
        else
        {
            $profile = UserProfile::getByPrimaryKey($user);
            $url = L_Utility::googleNewsRSS($query, $profile->local_territory);
        }

        $this->view()->feed = $this->formatFeedAdd($this->feedDiscoverAndAdd($url, $blog), $url);
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

        // tags (folders)
        $tag_ids = array_keys(UserBlogTag::getTagsHash($this->request()->folders, $user, $blog));

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
            UserBlogFeedTag::setTagIDArray($feed->user_blog_feed_id, $tag_ids);

            $updated = array_merge($updated, array('feed' => $hash));
        }
        $this->view()->updated = $updated;
    }

    /**
     * OPML
     */
    public function A_opml()
    {
        $blog = $this->request()->blog;
        $fn = array_key_exists('opmlfile', $_FILES) ? $_FILES['opmlfile']['tmp_name'] : null;

        // $this->response()->setRedirect('/feed');
        if(is_uploaded_file($fn))
        {
            $o = new L_OPMLParser();
            $data = $o->Parse(file_get_contents($fn));

            if(is_array($data) && ($total=count($data))>0)
            {
                for($j=0;$j<$total;$j++)
                {
                    if(array_key_exists('XMLURL', $data[$j]))
                    {
                        $title = array_key_exists('TITLE', $data[$j]) ? $data[$j]['TITLE'] : '';
                        $link = array_key_exists('HTMLURL', $data[$j]) ? $data[$j]['HTMLURL'] : '';
                        $this->feedAdd($blog, $data[$j]['XMLURL'], $title, $link, true);
                    }
                }
            }
        }

        $this->response()->setRedirect('/feed');
    }
}
