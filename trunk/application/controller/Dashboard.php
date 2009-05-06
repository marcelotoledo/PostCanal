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
        $id = $this->session()->user_profile_id;
        $blogs = UserBlog::findByUserProfileId($id, $enabled=true);
        $this->view()->blogs = $blogs;
    }
}
