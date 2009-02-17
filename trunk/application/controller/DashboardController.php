<?php

/**
 * Dashboard controller class
 * 
 * @category    Blotomate
 * @package     Controller
 */
class DashboardController extends AbstractController
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
        $id = $this->user_profile_id;
        $profile = UserProfile::findByPrimaryKeyEnabled($id);

        if(empty($profile))
        {
            $this->sessionDestroy();
            $this->setResponseRedirect(BASE_URL);
        }

        $information = UserInformation::findByPrimaryKey($id);
        $cms = UserCMS::findByUserProfileId($id);

        $this->setViewParameter('profile', $profile);
        $this->setViewParameter('information', $information);
        $this->setViewParameter('cms', $cms);
    }
}
