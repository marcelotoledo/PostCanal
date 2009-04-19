<?php

/**
 * Blog controller class
 * 
 * @category    Blotomate
 * @package     Controller
 * @author      Rafael Castilho <rafael@castilho.biz>
 */
class C_Blog extends C_Abstract
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

        $_type = $this->request()->blog_type;
        $_version = $this->request()->blog_version;
        $_revision = $this->request()->blog_revision;

        if(!is_object($blog_type = BlogType::findByName($_type, $_version)))
        {
            $_m = "blog type not found using " .
                  "type (" . $_type. ") and " .
                  "version (" . $_version . ")";
            $_d = array ('method' => __METHOD__);
            throw new B_Exception($_m, E_USER_WARNING, $_d);
        }

        $blog = new UserBlog();
        $blog->user_profile_id = $this->session()->user_profile_id;
        $blog->blog_type_id = $blog_type->blog_type_id;
        $blog->blog_type_revision = $_revision;
        $blog->name = $this->request()->name;
        $blog->url = $this->request()->url;
        $blog->manager_url = $this->request()->manager_url;
        $blog->manager_username = $this->request()->username;
        $blog->manager_password = $this->request()->password;

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
        $this->view()->result = BlogType::checkAdmin($url, $blog_type, $blog_version);
    }
}
