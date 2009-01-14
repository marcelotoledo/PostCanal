<?php

/**
 * Dashboard controller class
 * 
 * @category    Autoblog
 * @package     Controller
 */
class DashboardController extends AB_Controller
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
        $this->sessionCheck();
    }

    /**
     * Default action
     *
     * @return void
     */
    public function indexAction()
    {
    }
}
