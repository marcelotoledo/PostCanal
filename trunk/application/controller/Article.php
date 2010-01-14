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
        $ct = $zd->toString('YYYMMMdd');
        $zd_cfg = B_Registry::get('zend/date');

        foreach($results as $a)
        {
            $ts = intval(strtotime($a['article_date']));
            $lt = L_Utility::literalTime($ts - time());
            $zd->setTimestamp($ts);

            $local = $zd->toString($zd->toString('YYYMMMdd')==$ct ? 
                $zd_cfg->formatShort : 
                $zd_cfg->formatLong);

            $articles[] = array_merge($a, array
            (
                'article_time' => $ts,
                'article_time_literal' => $lt,
                'article_date_local' => $local
            ));
        }

        return $articles;
    }

    /**
     * List articles for a specified user blog feed
     *
     */
    public function A_feed()
    {
        $blog_hash = $this->request()->blog;
        $feed_hash = $this->request()->feed;
        $older = intval($this->request()->older);
        $user_id = $this->session()->user_profile_id;

        $this->view()->articles = $this->formatArticles(
            UserBlogFeed::findArticlesThreaded($blog_hash, $user_id, $feed_hash, $older));
        $this->view()->unread = UserBlogFeed::findTotalUnread($blog_hash, $user_id, $feed_hash);

        if($older>0) { $this->view()->append = true; }

        $this->session()->user_blog_hash = $blog_hash;
    }

    /**
     * List articles for a specified tag
     *
     */
    public function A_tag()
    {
        $blog  = $this->request()->blog;
        $tag   = $this->request()->tag;
        $older = intval($this->request()->older);
        $user  = $this->session()->user_profile_id;

        $this->view()->articles = $this->formatArticles(
            UserBlogFeed::findArticlesTag($blog, $user, $tag, $older));
        $this->view()->unread = UserBlogFeed::findTotalUnread($blog, $user, null, $tag);

        if($older>0) { $this->view()->append = true; }

        $this->session()->user_blog_hash = $blog;
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
        $this->view()->unread = UserBlogFeed::findTotalUnread($blog_hash, $user_id);

        if($older>0) { $this->view()->append = true; }

        $this->session()->user_blog_hash = $blog_hash;
    }

    /**
     * List writing articles
     *
     */
    public function A_writing()
    {
        $blog_hash = $this->request()->blog;
        $older = intval($this->request()->older);
        $user_id = $this->session()->user_profile_id;

        $this->view()->articles = $this->formatArticles(
            UserBlogFeed::findWritings($blog_hash, $user_id, $older));

        if($older>0) { $this->view()->append = true; }

        $this->session()->user_blog_hash = $blog_hash;
    }

    /**
     * create / update writings
     */
    public function A_save()
    {
        $blog = $this->request()->blog;
        $user = $this->session()->user_profile_id;

        $article         = $this->request()->article;
        $article_title   = $this->request()->article_title;
        $article_content = $this->request()->article_content;

        $art = null;

        if(strlen($article)>0) 
            $art = AggregatorFeedArticle::getWritingArticle($user, $blog, $article);

        if(is_object($art)==false)
        {
            $af = AggregatorFeed::getByURL(
                sprintf(AggregatorFeed::WRITINGS_URL_BASE, $user, $blog));

            $art = new AggregatorFeedArticle();
            $art->aggregator_feed_id = $af->aggregator_feed_id;
            $art->article_date = time();
            $art->article_link = '';
            $art->article_author = '';
        }

        $art->article_title   = $article_title;
        $art->article_content = $article_content;
        $art->save();

        $this->A_writing();
    }

    /**
     * delete writings
     */
    public function A_delete()
    {
        $blog    = $this->request()->blog;
        $user    = $this->session()->user_profile_id;
        $article = $this->request()->article;

        if(is_object(($a = AggregatorFeedArticle::getWritingArticle($user, $blog, $article))))
        {
            $a->delete();
        }

        $this->A_writing();
    }
}
