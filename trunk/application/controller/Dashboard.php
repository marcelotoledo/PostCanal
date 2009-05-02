<?php

/**
 * Dashboard controller class
 * 
 * @category    Blotomate
 * @package     Application Controller
 * @author      Rafael Castilho <rafael@castilho.biz>
 */

class C_Dashboard extends B_Controller
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
        $id = intval($this->session()->user_profile_id);
        $profile = ($id > 0) ? UserProfile::findByPrimaryKeyEnabled($id) : null;

        if(is_object($profile) == false)
        {
            $_m = "unable to retrieve user profile with id (" . $id . ")";
            $_d = array('method' => __METHOD__);
            throw new B_Exception($_m, E_USER_WARNING, $d);
        }

        $blogs = UserBlog::findByUserProfileId($id, $enabled=true);

        $this->view()->profile = $profile;
        $this->view()->blogs = $blogs;
    }
}
