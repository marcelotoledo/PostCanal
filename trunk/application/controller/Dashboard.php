<?php

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
     * Configure controller
     */
    public function configure($action_name)
    {
        $this->hasTranslation(false);
    }

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
        echo "deprecated\n";
        exit(1);

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
