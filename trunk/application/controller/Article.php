<?php

/**
 * Article controller class
 * 
 * @category    PostCanal
 * @package     Application Controller
 */

class C_Article extends B_Controller
{
    /**
     * Before action
     */
    public function before()
    {
        $this->authorize();
        $this->response()->setXML(true);
    }

    /**
     * List articles for a specified user blog feed
     *
     */
    public function A_threaded()
    {
        $blog_hash = $this->request()->blog;
        $feed_hash = $this->request()->feed;
        $older = strtotime($this->request()->older);
        $user_id = $this->session()->user_profile_id;

        $this->view()->articles = UserBlogFeed::findArticlesThreaded
        (
            $blog_hash, $user_id, $feed_hash, $older
        );

        $this->session()->user_blog_hash = $blog_hash;
    }

    /**
     * List articles for all user blog feeds
     *
     */
    public function A_all()
    {
        $this->response()->setXML(true);

        $blog_hash = $this->request()->blog;
        $older = strtotime($this->request()->older);
        $user_id = $this->session()->user_profile_id;

        $this->view()->articles = UserBlogFeed::findArticlesAll
        (
            $blog_hash, $user_id, $older
        );

        $this->session()->user_blog_hash = $blog_hash;
    }
}
