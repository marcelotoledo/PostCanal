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
        $id = $this->session()->user_profile_id;
        $this->view()->profile_preference = UserProfile::getPreference($id);
    }

    /**
     * Feed import
     */
    protected static function importFromFile($filename, $filetype, $filesize)
    {
        $results = array();

        if(file_exists($filename) && 
           is_readable($filename) &&
           $filesize < 2e05 && 
           strtolower($filetype) == 'text/xml')
        {
            $opml = new A_OPML();
            $f = fopen($filename, "r");
            $opml->Parse(fread($f, $filesize));
            fclose($f);
            unlink($filename);

            foreach($opml->data as $i)
            {
                $_u = addslashes($i['feeds']);
                $_t = addslashes($i['names']);

                if($_u && $_t)
                {
                    $results[] = array('url' => $_u, 'title' => $_t);
                }
            }
        }

        return $results;
    }

    /**
     * List feeds
     */
    public function A_index()
    {
        $this->view()->setLayout('dashboard');

        /* feed import from file upload */

        $this->view()->import = array();

        if($this->request()->getMethod() == B_Request::METHOD_POST &&
           array_key_exists('feedimportfile', $_FILES))
        {
            $f = $_FILES['feedimportfile'];
            $this->view()->import = self::importFromFile($f['tmp_name'], 
                                                         $f['type'], 
                                                         $f['size']);
        }

        $id = $this->session()->user_profile_id;
        $blogs = UserBlog::findByUser($id, $enabled=true);
        $this->view()->blogs = $blogs;
    }

    /**
     * List feeds
     */
    public function A_list()
    {
        $this->response()->setXML(true);
        $blog_hash = $this->request()->blog;
        $user_id = $this->session()->user_profile_id;
        $this->view()->feeds = UserBlogFeed::findAssocByBlogAndUser($blog_hash, $user_id);

        // wrong place!
        ////$this->session()->user_blog_hash = $blog_hash;
        ////$this->session()->dashboard_feed_display = 'thr';
    }

    /**
     * Discover feeds from URL
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
                throw new B_Exception($_m, E_USER_WARNING, $_d);
            }

            if(is_object($blog_feed) == true)
            {
                $blog_feed->deleted = false;
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
        $this->view()->feed = is_object($blog_feed) ? $blog_feed->hash : '';
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
     * Feed update column
     */
    protected static function updateColumn($user, $blog, $feed, $name, $value)
    {
        $result = "";

        if(is_object(($_o = UserBlogFeed::getByBlogAndFeedHash($user, $blog, $feed))))
        {
            $_o->{$name} = $value;
            $_o->save();
            $result = $feed;
        }

        return $result;
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
        $this->view()->result = self::updateColumn($user, $blog, $feed, 'enabled', $enable);
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
        $this->view()->result = self::updateColumn($user, $blog, $feed, 'deleted', true);
    }

    /**
     * Feed Update
     */
    public function A_update()
    {
        $this->response()->setXML(true);
        $blog = $this->request()->blog;
        $feed = $this->request()->feed;
        $user = $this->session()->user_profile_id;
        $name = $this->request()->k;
        $value = $this->request()->v;

        $result = array('feed' => $feed);

        if(in_array($name, array('feed_title'))) /* allowed columns */
        {
            if(self::updateColumn($user, $blog, $feed, $name, $value) != "")
            {
                $result = array_merge($result, array($name => $value));
            }
        }

        $this->view()->result = $result;
    }

    /**
     * Import feed from URL (discover + add)
     */
    public function A_import()
    {
        $this->response()->setXML(true);

        $url = $this->request()->url;
        $title = $this->request()->title;
        $blog_hash = $this->request()->blog;

        $result = current(AggregatorFeed::discover($url));

        if(array_key_exists('feed_url', $result))
        {
            $url = $result['feed_url'];
        }

        $blog_feed = $this->feedAdd($url, $title, $blog_hash);

        $this->view()->added = is_object($blog_feed);
    }
}
