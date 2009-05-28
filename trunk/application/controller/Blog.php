<?php

/**
 * Blog controller class
 * 
 * @category    PostCanal
 * @package     Application Controller
 * @author      Rafael Castilho <rafael@castilho.biz>
 */

class C_Blog extends B_Controller
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
     */
    public function A_index()
    {
        $this->view()->setLayout('dashboard');
        $user_id = $this->session()->user_profile_id;
        $this->view()->blogs_ = UserBlog::findByUser($user_id, true);
    }

    /**
     * Add action
     *
     * @return void
     */
    public function A_add()
    {
        $this->request()->getMethod() == B_Request::METHOD_POST ?
            $this->P_add() :
            $this->G_add();
    }

    /**
     * Add action, method GET
     */
    private function G_add()
    {
        $this->view()->setLayout('dashboard');
        $this->view()->blog = new UserBlog();
    }

    /**
     * Add action, method POST (save)
     *
     * @return void
     */
    private function P_add()
    {
        $this->response()->setXML(true);

        $added = false;

        $_type     = $this->request()->blog_type;
        $_version  = $this->request()->blog_version;

        if(!is_object($blog_type = BlogType::getByName($_type, $_version)))
        {
            $_m = "blog type not found using " .
                  "type (" . $_type. ") and " .
                  "version (" . $_version . ")";
            $_d = array ('method' => __METHOD__);
            throw new B_Exception($_m, E_USER_WARNING, $_d);
        }

        $blog = new UserBlog();
        $blog->user_profile_id    = $this->session()->user_profile_id;
        $blog->blog_type_id       = $blog_type->blog_type_id;
        $blog->name               = $this->request()->blog_name;
        $blog->blog_url           = $this->request()->blog_url;
        $blog->blog_manager_url   = $this->request()->blog_manager_url;
        $blog->blog_username      = $this->request()->blog_username;
        $blog->blog_password      = $this->request()->blog_password;
        $blog->blog_type_revision = $this->request()->blog_revision;

        try
        {
            $blog->save();
            $added = true;
        }
        catch(B_Exception $exception)
        {
            $_m = "failed to add new blog";
            $_d = array('method' => __METHOD__);
            B_Exception::forward($_m, E_USER_WARNING, $exception, $_d);
        }

        $this->view()->added = $added;
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
     * check URL admin
     */
    public function A_check()
    {
        $this->response()->setXML(true);
        $url = $this->request()->url;
        $blog_type = $this->request()->type;
        $blog_version = $this->request()->version;
        $this->view()->result = BlogType::checkManagerUrl($url, $blog_type, $blog_version);
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
                if(strlen($this->request()->{$k})>0)
                {
                    $blog->{$k} = $this->request()->{$k};
                    $updated = array_merge($updated, array($k => $blog->{$k}));
                }
            }
            $blog->save();
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

        if(is_object(($blog = UserBlog::getByUserAndHash($user, $hash))))
        {
            $blog->deleted = true;
            $blog->save();
            $result = $hash;
        }

        $this->view()->result = $result;
    }
}
