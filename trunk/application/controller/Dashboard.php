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
        if($this->request->getAction() != 'unauthorized')
        {
            $url = ($this->request->getAction() == "index") ? 
                B_Request::url('index','index') : 
                B_Request::url('dashboard', 'unauthorized');
            $this->authorize($url);
        }
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

        $cms = UserCMS::findByUserProfileId($id);

        $this->view->profile = $profile;
        $this->view->cms = $cms;
    }

    /**
     * Load CMS data
     *
     */
    public function A_cms()
    {
        $this->view->setLayout(null);

        $user_profile_id = intval($this->session->user_profile_id);
        $cid = $this->request->cid;
        $cms = null;
        $this->view->channels = array();

        if($user_profile_id > 0 && strlen($cid) > 0)
        {
            $cms = UserCMS::findByCID($user_profile_id, $cid);

            if(is_object($cms))
            {
                $this->view->channels = UserCMSChannel::findByUserCMS($cms->user_cms_id);
            }
        }

        $this->view->cms = $cms;
    }

    /**
     * Load channel data
     *
     */
    public function A_channel()
    {
        $this->view->setLayout(null);

        $user_profile_id = intval($this->session->user_profile_id);
        $cid = $this->request->cid;
        $ch = $this->request->ch;
        $cms = null;
        $channel = null;
        $this->view->items = array();

        if($user_profile_id > 0 && strlen($cid) > 0)
        {
            $cms = UserCMS::findByCID($user_profile_id, $cid);

            if(is_object($cms) && strlen($ch) > 0)
            {
                $channel = UserCMSChannel::findByCH($cms->user_cms_id, $ch);

                if(is_object($channel))
                {
                    $chid = $channel->aggregator_channel_id;
                    $this->view->items = AggregatorItem::findByChannel($chid);
                }
            }
        }
    }

    /**
     * Load queue data
     *
     */
    public function A_queue()
    {
        $this->view->setLayout(null);

        $user_profile_id = intval($this->session->user_profile_id);
        $cid = $this->request->cid;
        $cms = null;
        $this->view->items = array();

        if($user_profile_id > 0 && strlen($cid) > 0)
        {
            $cms = UserCMS::findByCID($user_profile_id, $cid);

            if(is_object($cms))
            {
                $this->view->items = UserCMSQueue::findByUserCMS($cms->user_cms_id);
            }
        }
    }

    /**
     * Unauthorized
     */
    public function A_unauthorized()
    {
        $this->view->setLayout(null);
    }
}
