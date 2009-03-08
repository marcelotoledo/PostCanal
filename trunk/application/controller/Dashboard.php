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
            throw new AB_Exception($_m, E_USER_WARNING, $d);
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

        if($user_profile_id > 0 && strlen($cid) > 0)
        {
            $cms = UserCMS::findByCID($user_profile_id, $cid);
        }

        $this->view->cms = $cms;
    }
}
