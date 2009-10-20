<?php

/**
 * Reader controller class
 * 
 * @category    PostCanal
 * @package     Application Controller
 * @author      Rafael Castilho <rafael@castilho.biz>
 */

class C_Reader extends B_Controller
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

        $id = $this->session()->user_profile_id;
        $blogs = UserBlog::findByUser($id, $enabled=true);
        $this->view()->blogs = $blogs;
        $settings = UserDashboard::getByUser($id);
        $this->view()->settings = $settings;
        $blog_current = $settings->blog->current;

        if(count($blogs)==0)
        {
            header('Location: /site');
            exit(0);
        }

        $this->view()->total_feeds = UserBlogFeed::findTotalByBlogAndUser($blog_current,
                                                                          $id,
                                                                          true);
    }
}
