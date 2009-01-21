<?php

/**
 * Dashboard controller class
 * 
 * @category    Autoblog
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
        $this->getView()->setLayout('dashboard');
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
