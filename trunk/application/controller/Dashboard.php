<?php

/**
 * Dashboard controller class
 * 
 * @category    Blotomate
 * @package     Controller
 * @author      Rafael Castilho <rafael@castilho.biz>
 */
class C_Dashboard extends C_Abstract
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
        $id = intval($this->session->user_profile_id);
        $profile = ($id > 0) ? UserProfile::findByPrimaryKeyEnabled($id) : null;

        if(is_object($profile) == false)
        {
            $_m = "unable to retrieve user profile with id (" . $id . ")";
            $_d = array('method' => __METHOD__);
            throw new B_Exception($_m, E_USER_WARNING, $d);
        }

        $blogs = UserBlog::findByUserProfileId($id, true);

        $this->view->profile = $profile;
        $this->view->blogs = $blogs;
    }

    # /**
    #  * Load Blog data
    #  *
    #  */
    # public function A_blog()
    # {
    #     $this->view->setLayout(null);

    #     $user_profile_id = intval($this->session->user_profile_id);
    #     $hash = $this->request->blog;
    #     $blog = null;
    #     $this->view->feeds = array();

    #     if($user_profile_id > 0 && strlen($hash) > 0)
    #     {
    #         $blog = UserBlog::findByHash($user_profile_id, $hash);

    #         if(is_object($blog))
    #         {
    #             $this->view->feeds = UserBlogFeed::findByUserBlog($blog->user_blog_id);
    #         }
    #     }

    #     $this->view->blog = $blog;
    # }

    # /**
    #  * Load feed data
    #  *
    #  */
    # public function A_feed()
    # {
    #     $this->view->setLayout(null);

    #     $user_profile_id = intval($this->session->user_profile_id);
    #     $hash = $this->request->blog;
    #     $ch = $this->request->ch;
    #     $blog = null;
    #     $feed = null;
    #     $this->view->items = array();

    #     if($user_profile_id > 0 && strlen($hash) > 0)
    #     {
    #         $blog = UserBlog::findByHash($user_profile_id, $hash);

    #         if(is_object($blog) && strlen($ch) > 0)
    #         {
    #             $feed = UserBlogFeed::findByCH($blog->user_blog_id, $ch);

    #             if(is_object($feed))
    #             {
    #                 $chid = $feed->aggregator_feed_id;
    #                 $this->view->items = AggregatorItem::findByFeed($chid);
    #             }
    #         }
    #     }
    # }

    # /**
    #  * Load queue data
    #  *
    #  */
    # public function A_queue()
    # {
    #     $this->view->setLayout(null);

    #     $user_profile_id = intval($this->session->user_profile_id);
    #     $cid = $this->request->cid;
    #     $blog = null;
    #     $this->view->items = array();

    #     if($user_profile_id > 0 && strlen($cid) > 0)
    #     {
    #         $blog = UserBlog::findByHash($user_profile_id, $hash);

    #         if(is_object($blog))
    #         {
    #             $this->view->items = UserBlogQueue::findByUserBlog($blog->user_blog_id);
    #         }
    #     }
    # }

    # /**
    #  * Unauthorized
    #  */
    # public function A_unauthorized()
    # {
    #     $this->view->setLayout(null);
    # }
}
