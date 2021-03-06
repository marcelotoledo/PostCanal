<?php

/**
 * Queue controller class
 * 
 * @category    PostCanal
 * @package     Application Controller
 * @author      Rafael Castilho <rafael@castilho.biz>
 */

class C_Queue extends B_Controller
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
     *
     * @return void
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
     * List blog entries
     *
     * @return void
     */
    public function A_list()
    {
        $this->response()->setXML(true);
        $blog_hash = $this->request()->blog;
        $profile_id = $this->session()->user_profile_id;

        $queue = BlogEntry::findQueue($profile_id, $blog_hash);
        $published = BlogEntry::findPublished($profile_id, $blog_hash);

        $results = array('queue' => array(), 'published' => array());

        $zd = new Zend_Date(time(), false, $this->session()->getCulture());
        $zd->setTimezone($this->session()->getTimezone());
        $ct = $zd->toString('YYYMMMdd');
        $zd_cfg = B_Registry::get('zend/date');

        foreach($queue as $o)
        {
            $zd->setTimestamp($o->publication_date);
            $diff = $o->publication_date - time();
            $lt = L_Utility::literalTime($diff);

            $local = $zd->toString($zd->toString('YYYMMMdd')==$ct ? 
                $zd_cfg->formatShort : 
                $zd_cfg->formatLong);

            $results['queue'][] = array
            (
                'entry'                    => $o->hash,
                'entry_title'              => $o->entry_title,
                'entry_content'            => $o->entry_content,
                'publication_status'       => $o->publication_status,
                'publication_date'         => $o->publication_date,
                'publication_date_diff'    => $diff,
                'publication_date_literal' => $lt,
                'publication_date_local'   => $local,
                'ordering'                 => $o->ordering
            );
        }

        foreach($published as $o)
        {
            $zd->setTimestamp($o->publication_date);

            $diff = $o->publication_date - time();
            $lt = L_Utility::literalTime($diff);

            $local = $zd->toString($zd->toString('YYYMMMdd')==$ct ? 
                $zd_cfg->formatShort : 
                $zd_cfg->formatLong);

            $results['published'][] = array
            (
                'entry'                    => $o->hash,
                'entry_title'              => $o->entry_title,
                'entry_content'            => $o->entry_content,
                'publication_status'       => $o->publication_status,
                'publication_date'         => $o->publication_date,
                'publication_date_diff'    => $diff,
                'publication_date_literal' => $lt,
                'publication_date_local'   => $local,
                'ordering'                 => $o->ordering
            );
        }

        $this->view->result = $results;
    }

    /**
     * Add item to queue
     *
     * @return void
     */
    public function A_add()
    {
        $this->response()->setXML(true);

        $article_md5 = $this->request()->article;
        $blog_hash = $this->request()->blog;
        $feed_hash = $this->request()->feed;
        $profile_id = $this->session()->user_profile_id;

        $entry = BlogEntry::newFromFeedArticle($article_md5,
                                               $blog_hash,
                                               $feed_hash,
                                               $profile_id);

        $zd = new Zend_Date(time(), false, $this->session()->getCulture());
        $zd->setTimezone($this->session()->getTimezone());
        $zd->setTimestamp($entry['publication_date']);

        if($entry)
        {
            $this->view()->result = array('feed'    => $feed_hash,
                                          'article' => $article_md5,
                                          'entry'   => $entry['entry']);
        }
    }

    /**
     * Publish queue item
     *
     * @return void
     */
    public function A_publish()
    {
        $this->response()->setXML(true);

        $blog_hash = $this->request()->blog;
        $entry_hash = $this->request()->entry;
        $profile_id = $this->session()->user_profile_id;

        $this->view()->result = 
            BlogEntry::updateEntryToPublish($entry_hash, $blog_hash, $profile_id) ?
            $entry_hash : "";
    }

    /**
     * Publish queue item
     *
     * @return void
     */
    public function A_check()
    {
        $this->response()->setXML(true);

        $waiting = explode(",", $this->request()->waiting);
        $blog_hash = $this->request()->blog;
        $profile_id = $this->session()->user_profile_id;

        $this->view->result = BlogEntry::checkStatus($waiting,
                                                     $blog_hash, 
                                                     $profile_id);
    }

    /**
     * Set publication automatic
     *
     * @return void
     */
    public function A_auto()
    {
        $this->response()->setXML(true);

        $blog_hash = $this->request()->blog;
        $queue_publication = $this->request()->publication==1 ? true : false;
        $queue_interval = intval($this->request()->interval);
        $profile_id = $this->session()->user_profile_id;

        BlogEntry::updateAutoPublication($blog_hash, 
                                         $profile_id, 
                                         $queue_publication, 
                                         $queue_interval);
    }

    /**
     * Feed update ordering position
     */
    public function A_position()
    {
        $this->response()->setXML(true);

        $blog_hash = $this->request()->blog;
        $entry_hash = $this->request()->entry;
        $profile_id = $this->session()->user_profile_id;
        $position = $this->request()->position;

        BlogEntry::updateOrdering($blog_hash, $profile_id, $entry_hash, $position);

        $this->view()->updated = true;
    }

    /**
     * Feed delete
     */
    public function A_delete()
    {
        $this->response()->setXML(true);
        $blog = $this->request()->blog;
        $entry = $this->request()->entry;
        $user = $this->session()->user_profile_id;

        if(strlen(($article = BlogEntry::deleteEntry($user, $blog, $entry)))>0)
        {
            $this->view->entry = $entry;
            $this->view->article = $article;
        }
    }

    /**
     * Feed Update
     */
    public function A_update()
    {
        $this->response()->setXML(true);

        $blog = $this->request()->blog;
        $entry = $this->request()->entry;
        $user = $this->session()->user_profile_id;
        $result = array();

        if(is_object(($e = BlogEntry::getByBlogAndEntryHash($user, $blog, $entry))))
        {
            if($e->publication_status != BlogEntry::STATUS_PUBLISHED)
            {
                $e->entry_title = $this->request()->title;
                $e->entry_content = $this->request()->content;
                $e->save();
            }

            $result = array
            (
                'entry'   => $e->hash,
                'title'   => $e->entry_title,
                'content' => $e->entry_content
            );
        }

        $this->view()->result = $result;
    }
}
