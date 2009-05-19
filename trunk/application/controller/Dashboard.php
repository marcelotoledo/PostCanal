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
        $id = $this->session()->user_profile_id;
        $this->view()->profile_preference = UserProfile::getPreference($id);
    }

    /**
     * Default action
     *
     * @return void
     */
    public function A_index()
    {
        $id = $this->session()->user_profile_id;
        $blogs = UserBlog::findByUser($id, $enabled=true);
        $this->view()->blogs = $blogs;
    }
}
