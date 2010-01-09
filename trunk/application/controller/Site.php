<?php

/**
 * Site controller class
 * 
 * @category    PostCanal
 * @package     Application Controller
 * @author      Rafael Castilho <rafael@castilho.biz>
 */

class C_Site extends B_Controller
{
    /**
     * Discover status
     */
    const ADD_STATUS_OK          = 'ok';
    const ADD_STATUS_FAILED      = 'failed';
    const ADD_STATUS_TIMEOUT     = 'timeout';
    const ADD_STATUS_OVERQUOTA   = 'overquota'; // TODO
    const ADD_STATUS_URL_FAILED  = 'url_failed';
    const ADD_STATUS_TYPE_FAILED = 'type_failed';
    const ADD_STATUS_MAINTENANCE = 'maintenance';


    /**
     * Before action
     */
    public function before()
    {
        $this->authorize();
    }

    /**
     * Default action
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
     * Format blog object for ajax response
     */
    private static function objResponse($blog)
    {
        return array
        (
            'blog'          => $blog->hash,
            'name'          => $blog->name,
            'url'           => $blog->blog_url,
            'username'      => $blog->blog_username,
            'oauth_enabled' => $blog->oauth_enabled ? 'true' : 'false',
            'keywords'      => $blog->keywords
        );
    }

    /**
     * List blogs
     */
    public function A_list()
    {
        $this->response()->setXML(true);

        $this->view()->setLayout('dashboard');
        $user_id = $this->session()->user_profile_id;

        $blogs = array();

        foreach(UserBlog::findByUser($user_id, true) as $b)
        {
            $blogs[] = self::objResponse($b);
        }

        $this->view()->blogs = $blogs;
    }

    /**
     * Add action
     *
     * @return void
     */
    public function A_add()
    {
        $this->response()->setXML(true);
        $url = $this->request()->url;
        $profile_id = $this->session()->user_profile_id;
        $quota = $this->session()->user_profile_quota_blog;
        $blog = null;

        /* check blog quota */
        if($quota > 0 && UserBlog::total($profile_id) >= $quota)
        {
            $this->view()->status = self::ADD_STATUS_OVERQUOTA;
            return false;
        }

        /* discover blog type */

        if(!is_object(($discover = BlogType::discover($url))))
        {
            $this->view()->status = self::ADD_STATUS_TIMEOUT;
            return false;
        }

        $this->view()->type_name = $discover->type_name;

        $status = self::ADD_STATUS_FAILED;

        if($discover->url_accepted == false)
        {
            $status = self::ADD_STATUS_URL_FAILED;
        }
        elseif($discover->type_accepted == false)
        {
            $status = self::ADD_STATUS_TYPE_FAILED;
            $_m = sprintf('unsuported blog type for url (%s)', $url);
            $_d = array('method' => __METHOD__, 'user_profile_id' => $profile_id);
            B_Log::write($_m, E_NOTICE, $_d);
        }
        elseif($discover->maintenance == true)
        {
            $status = self::ADD_STATUS_MAINTENANCE;
        }
        else
        {
            $status = self::ADD_STATUS_OK;

            $blog = new UserBlog();
            $blog->user_profile_id    = $profile_id;
            $blog->blog_type_id       = $discover->blog_type_id;
            $blog->blog_url           = $discover->url;
            $blog->name               = $discover->title;
            $blog->blog_manager_url   = $discover->manager_url;
            $blog->blog_type_revision = $discover->revision;
            $blog->blog_username      = $discover->username;
            $blog->blog_password      = $discover->password;
            $blog->oauth_enabled      = $discover->oauth_enabled;

            try
            {
                $blog->save();
            }
            catch(B_Exception $exception)
            {
                $_m = "failed to add new blog";
                $_d = array('method' => __METHOD__);
                B_Exception::forward($_m, E_WARNING, $exception, $_d);
            } 

            // create blog's writings feed
            $wf = new AggregatorFeed();
            $wf->feed_url = sprintf(AggregatorFeed::WRITINGS_URL_BASE, $profile_id, $blog->hash);
            $wf->feed_url_md5 = md5($wf->feed_url);
            $wf->feed_title = AggregatorFeed::WRITINGS_TITLE;
            $wf->feed_link = "";
            $wf->feed_description = "";
            $wf->updateable = false;
            $wf->save();

            // add blog's writings feed to user blog feeds
            $bf = new UserBlogFeed();
            $bf->user_blog_id = $blog->user_blog_id;
            $bf->aggregator_feed_id = $wf->aggregator_feed_id;
            $bf->feed_title = $wf->feed_title;
            $bf->feed_description = $wf->feed_description;
            $bf->ordering = 0;
            $bf->visible = false;
            $bf->save();
        }

        $this->view()->status = $status;

        if(is_object($blog))
        {
            $this->view()->result = self::objResponse($blog);
        }
    }

    /**
     * discover blog type
     */
    public function A_discover()
    {
        $this->response()->setXML(true);
        $url = $this->request()->url;
        $this->view()->result = BlogType::discover($url);
    }

    /**
     * update blog
     */
    public function A_update()
    {
        $this->response()->setXML(true);
        $hash = $this->request()->blog;
        $user = $this->session()->user_profile_id;
        $updated = array();

        if(is_object(($blog = UserBlog::getByUserAndHash($user, $hash))))
        {
            foreach(UserBlog::$allow_write as $k)
            {
                if(array_key_exists($k, $_REQUEST) && strlen($_REQUEST[$k])>0)
                {
                    $blog->{$k} = $this->request()->{$k};
                    $updated = array_merge($updated, array($k => $blog->{$k}));
                }
            }
            $blog->save();
            $updated = array_merge($updated, array('blog' => $hash));
        }
        $this->view()->updated = $updated;
    }

    /**
     * delete blog
     */
    public function A_delete()
    {
        $this->response()->setXML(true);
        $hash = $this->request()->blog;
        $user = $this->session()->user_profile_id;
        $result = "";

        if(UserBlog::deleteByUserAndHash($user, $hash)) $result = $hash;

        $this->view()->result = $result;
    }

    /**
     * load blog info
     */
    public function A_load()
    {
        $this->response()->setXML(true);
        $hash = $this->request()->blog;
        $user = $this->session()->user_profile_id;
        $result = array('name'                 =>   "" ,
                        'blog_url'             =>   "" ,
                        'enqueueing_auto'      =>    0 ,
                        'publication_auto'     =>    0 ,
                        'publication_interval' => 3600 ,
                        'keywords'             =>   "" );

        if(is_object(($blog = UserBlog::getByUserAndHash($user, $hash))))
        {
            foreach(array_keys($result) as $k)
            {
                $result[$k] = $blog->{$k};
            }
        }

        $this->view()->result = $result;
    }

    /**
     * Authorize OAuth
     */
    public function A_authorize()
    {
        $hash = $this->request()->blog;
        $user = $this->session()->user_profile_id;

        if(is_object(($blog = UserBlog::getByUserAndHash($user, $hash))))
        {
            $type = BlogType::getByPrimaryKey($blog->blog_type_id);
            $config = B_Registry::get('oauth/' . $type->type_name);
            $url = ($config->authorizeURL . '?oauth_token=' . $blog->blog_username);
            $this->response()->setRedirect($url, 301);
        }
        else
        {
            $this->response()->setRedirect('/site');
        }
    }
}
