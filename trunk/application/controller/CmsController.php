<?php

/**
 * Cms controller class
 * 
 * @category    Blotomate
 * @package     Controller
 */
class CmsController extends SessionController
{
    /**
     * check url response constants 
     */
    const URL_BASE_OK        = "url_base_ok";
    const URL_BASE_FAILED    = "url_base_failed";
    const URL_BASE_ERROR_3XX = "url_base_error_3xx";
    const URL_BASE_ERROR_4XX = "url_base_error_4xx";
    const URL_BASE_ERROR_5XX = "url_base_error_5xx";


    /**
     * Cms controller constructor
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
     * Add action
     *
     * @return void
     */
    public function addAction()
    {
        $this->setViewLayout('dashboard');
        $this->setViewParameter('cms', new UserCMS());
    }

    /**
     * Check URL base action
     *
     * @return void
     */
    public function checkUrlBaseAction()
    {
        $this->setViewLayout(null);
        $this->setViewTemplate(null);
        $this->setViewData(self::URL_BASE_OK);
    }
}
