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
     * List blog entries
     *
     * @return void
     */
    public function A_list()
    {
        $this->response()->setXML(true);
        $blog_hash = $this->request()->blog;
        $profile_id = $this->session()->user_profile_id;
        $results = BlogEntry::findQueueByUserAndBlog($profile_id, $blog_hash);

        $zd = new Zend_Date(time(), false, $this->session()->getCulture());
        $zd->setTimezone($this->session()->getTimezone());

        for($i=0;$i<count($results['queue']);$i++)
        {
            $zd->setTimestamp(strtotime($results['queue'][$i]['publication_date']));
            $results['queue'][$i]['publication_date_local'] = $zd->toString();
        }

        for($i=0;$i<count($results['published']);$i++)
        {
            $zd->setTimestamp(strtotime($results['published'][$i]['publication_date']));
            $results['published'][$i]['publication_date_local'] = $zd->toString();
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
        $entry['publication_date_local'] = $zd->toString();

        $this->view->result = $entry;
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
        /*
        $blog = $this->request()->blog;
        $feed = $this->request()->feed;
        $user = $this->session()->user_profile_id;
        $this->view()->result = UserBlogFeed::updateColumn($user, $blog, $feed, 'deleted', true);
        */
    }

    /**
     * Feed Update
     */
    public function A_update()
    {
        $this->response()->setXML(true);
        /*
        $blog = $this->request()->blog;
        $feed = $this->request()->feed;
        $user = $this->session()->user_profile_id;
        $updated = array();

        if(is_object(($feed = UserBlogFeed::getByBlogAndFeedHash($user, $blog, $feed))))
        {
            foreach(UserBlogFeed::$allow_write as $k)
            {
                if(strlen($this->request()->{$k})>0)
                {
                    $feed->{$k} = $this->request()->{$k};
                    $updated = array_merge($updated, array($k => $feed->{$k}));
                }
            }
            $feed->save();
        }
        $this->view()->updated = $updated;
        */
    }
}
