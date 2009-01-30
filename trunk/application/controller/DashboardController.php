<?php

/**
 * Dashboard controller class
 * 
 * @category    Blotomate
 * @package     Controller
 */
class DashboardController extends SessionController
{
    /**
     * Dashboard controller constructor
     *
     * @param   AB_Request  $request
     * @param   AB_Response $response
     * @return  void
     */
    public function __construct($request, $response)
    {
        parent::__construct($request, $response);
        $this->setViewLayout('dashboard');
        $this->sessionAuthorize();
    }

    /**
     * Default action
     *
     * @return void
     */
    public function indexAction()
    {
        $profile = UserProfile::findByPrimaryKey($this->user_profile_id);

        if(empty($profile))
        {
            $this->sessionDestroy();
            $this->setResponseRedirect(BASE_URL);
        }
    }
}