<?php

/**
 * Article controller class
 * 
 * @category    Blotomate
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
    public function A_list()
    {
        $blog_hash = $this->request()->blog;
        $feed_hash = $this->request()->feed;
        $start_time = $this->request()->time;
        $user_id = $this->session()->user_profile_id;

        $this->view()->articles = UserBlogFeed::partialArticles($blog_hash, 
                                                                $user_id,
                                                                $feed_hash,
                                                                $start_time);
    }

    /**
     * List articles for all user blog feeds
     *
     */
    public function A_all()
    {
        $this->response()->setXML(true);

        $blog_hash = $this->request()->blog;
        $start_time = $this->request()->time;
        $user_id = $this->session()->user_profile_id;

        $this->view()->articles = UserBlogFeed::partialArticlesAll($blog_hash, 
                                                                   $user_id,
                                                                   $start_time);
    }
}
