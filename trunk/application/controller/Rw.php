<?php

/**
 * Reader/Writer controller class
 * 
 * @category    PostCanal
 * @package     Application Controller
 * @author      Rafael Castilho <rafael@castilho.biz>
 */

class C_Rw extends B_Controller
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

    /**
     * Set user blog feed article attribute
     */
    public function A_wr()
    {
        $this->response()->setXML(true);
        $u = $this->session()->user_profile_id;
        $b = $this->request()->blog;
        $a = $this->request()->article;
        $w =($this->request()->wr=='true');
        UserBlogFeedArticle::setArticleReadAttr($u, $b, $a, $w);
    }
}
