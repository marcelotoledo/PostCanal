<?php

/**
 * Queue controller class
 * 
 * @category    Blotomate
 * @package     Controller
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
     * List queue items
     *
     * @return void
     */
    public function A_list()
    {
        $this->response()->setXML(true);

        $blog_hash = $this->request()->blog;
        $user_profile_id = $this->session()->user_profile_id;

        $queue = array();

        foreach(QueueItem::findByUserBlog($user_profile_id, $blog_hash) as $_i)
        {
            $queue[] = array('item' => $_i->hash,
                             'item_title' => $_i->item_title,
                             'item_content' => $_i->item_content);
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

        $feed_item_md5 = $this->request()->item;
        $blog_hash = $this->request()->blog;
        $feed_hash = $this->request()->feed;
        $user_profile_id = $this->session()->user_profile_id;

        $queue_item = QueueItem::newFromFeedItem($feed_item_md5,
                                                 $blog_hash,
                                                 $feed_hash,
                                                 $user_profile_id);

        $this->view->result = array(
            'blog' => $blog_hash,
            'feed' => $feed_hash,
            'item' => $queue_item->hash,
            'item_title' => $queue_item->item_title,
            'item_content' => $queue_item->item_content
        );
    }
}
