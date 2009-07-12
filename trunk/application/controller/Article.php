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
     * Format article results
     */
    protected function formatArticles($results)
    {
        $articles = array();

        $zd = new Zend_Date(time(), false, $this->session()->getCulture());
        $zd->setTimezone($this->session()->getTimezone());

        foreach($results as $a)
        {
            $ts = strtotime($a['article_date']);
            $zd->setTimestamp($ts);

            $articles[] = array_merge($a, array
            (
                'article_time' => $ts,
                'article_date_local' => $zd->toString()
            ));
        }

        return $articles;
    }

    /**
     * List articles for a specified user blog feed
     *
     */
    public function A_threaded()
    {
        $blog_hash = $this->request()->blog;
        $feed_hash = $this->request()->feed;
        $older = intval($this->request()->older);
        $user_id = $this->session()->user_profile_id;

        $this->view()->articles = $this->formatArticles(
            UserBlogFeed::findArticlesThreaded($blog_hash, $user_id, $feed_hash, $older));

        if($older>0) { $this->view()->append = true; }

        $this->session()->user_blog_hash = $blog_hash;
    }

    /**
     * List articles for all user blog feeds
     *
     */
    public function A_all()
    {
        $blog_hash = $this->request()->blog;
        $older = intval($this->request()->older);
        $user_id = $this->session()->user_profile_id;

        $this->view()->articles = $this->formatArticles(
            UserBlogFeed::findArticlesAll($blog_hash, $user_id, $older));

        if($older>0) { $this->view()->append = true; }

        $this->session()->user_blog_hash = $blog_hash;
    }
}
