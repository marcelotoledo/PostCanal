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

        $ct = $this->session()->getCulture();
        $tz = $this->session()->getTimezone();

        foreach($results as $a)
        {
            $zd = new Zend_Date(strtotime($a['article_date']), false, $ct);
            $zd->setTimezone($tz);

            $articles[] = array_merge($a, array
            (
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
        $older = strtotime($this->request()->older);
        $user_id = $this->session()->user_profile_id;

        $this->view()->articles = $this->formatArticles(
            UserBlogFeed::findArticlesThreaded($blog_hash, $user_id, $feed_hash, $older));

        $this->session()->user_blog_hash = $blog_hash;
    }

    /**
     * List articles for all user blog feeds
     *
     */
    public function A_all()
    {
        $blog_hash = $this->request()->blog;
        $older = strtotime($this->request()->older);
        $user_id = $this->session()->user_profile_id;

        $this->view()->articles = $this->formatArticles(
            UserBlogFeed::findArticlesAll($blog_hash, $user_id, $older));

        $this->session()->user_blog_hash = $blog_hash;
    }
}
