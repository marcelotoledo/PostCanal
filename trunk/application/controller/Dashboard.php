<?php

echo 'deprecated';
exit(1);

/**
 * Dashboard controller class
 * 
 * @category    PostCanal
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
        $blogs = UserBlog::findByUser($id, $enabled=true);
        $this->view()->blogs = $blogs;
        $this->view()->settings = UserDashboard::getByUser($id);
    }

    /**
     * Save Setting
     */
    public function A_setting()
    {
        $this->response()->setXML(true);
        $id = $this->session()->user_profile_id;
        $context = $this->request()->context;
        $name = $this->request()->name;
        $value = $this->request()->value;
        UserDashboard::saveSetting($id, $context, $name, $value);
    }
}
