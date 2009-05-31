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
        $user_profile_id = $this->session()->user_profile_id;

        $queue = array();

        foreach(BlogEntry::findByUserAndBlogHash($user_profile_id, $blog_hash) as $_i)
        {
            $queue[] = array('item' => $_i->hash,
                             'item_title' => $_i->item_title,
                             'item_content' => $_i->item_content,
                             'publish_status' => $_i->publish_status,
                             'publish_date' => $_i->publish_date);
        }

        $this->view()->queue = $queue;
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

        $this->view->result = array(
            'blog'          => $blog_hash,
            'feed'          => $feed_hash,
            'entry'         => $entry->hash,
            'entry_title'   => $entry->entry_title,
            'entry_content' => $entry->entry_content
        );
    }

    /**
     * Publish queue item
     *
     * @return void
     */
    public function A_publish()
    {
        $this->response()->setXML(true);

        $item_hash = $this->request()->item;
        $blog_hash = $this->request()->blog;
        $user_profile_id = $this->session()->user_profile_id;

        BlogEntry::itemToPublish($item_hash, 
                                 $blog_hash, 
                                 $user_profile_id);
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
        $user_profile_id = $this->session()->user_profile_id;

        $this->view->result = BlogEntry::checkStatus($waiting,
                                                     $blog_hash, 
                                                     $user_profile_id);
    }
}
