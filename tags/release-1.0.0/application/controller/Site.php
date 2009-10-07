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
            'blog'     => $blog->hash,
            'name'     => $blog->name,
            'url'      => $blog->blog_url,
            'username' => $blog->blog_username,
            'keywords' => $blog->keywords
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
            $blog->user_profile_id = $this->session()->user_profile_id;
            $blog->blog_type_id    = $discover->blog_type_id;
            $blog->blog_url        = $discover->url;
            $blog->name            = $discover->title;

            if(strlen($blog->name)==0)
            {
                $blog->name = $this->translation()->my_new_site;
            }

            $blog->blog_manager_url = $discover->manager_url;
            $blog->blog_type_revision = $discover->revision;
            $blog->blog_username = $discover->username;

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
}
