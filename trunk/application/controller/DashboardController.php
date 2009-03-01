<?php

/**
 * Dashboard controller class
 * 
 * @category    Blotomate
 * @package     Controller
 * @author      Rafael Castilho <rafael@castilho.biz>
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
        $this->sessionAuthorize();
    }

    /**
     * Default action
     *
     * @return void
     */
    public function indexAction()
    {
        $this->setViewLayout('dashboard');

        $id = intval($this->user_profile_id);
        $profile = null;

        if($id > 0) $profile = UserProfile::findByPrimaryKeyEnabled($id);

        if(empty($profile))
        {
            $this->sessionDestroy();
            $this->setResponseRedirect(BASE_URL);
        }

        $information = null;
        $cms = null;

        if($id > 0)
        {
            $information = UserProfileInformation::findByPrimaryKey($id);
            $cms = UserCMS::findByUserProfileId($id);
        }

        $this->setViewParameter('profile', $profile);
        $this->setViewParameter('information', $information);
        $this->setViewParameter('cms', $cms);
    }

    /**
     * Load CMS data
     *
     */
    public function cmsAction()
    {
        $this->setViewLayout(null);

        $user_profile_id = intval($this->user_profile_id);
        $cid = $this->getRequestParameter('cid');
        $cms = null;

        if($user_profile_id > 0 && strlen($cid) > 0)
        {
            $cms = UserCMS::findByCID($user_profile_id, $cid);
        }

        $this->setViewParameter('cms', $cms);
    }
}
