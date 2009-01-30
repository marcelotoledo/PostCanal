<?php

/**
 * Index controller class
 * 
 * @category    Blotomate
 * @package     Controller
 */
class IndexController extends AB_Controller
{
    /**
     * Index controller constructor
     *
     * @param   AB_Request  $request
     * @param   AB_Response $response
     * @return void
     */
    public function __construct($request, $response)
    {
        parent::__construct($request, $response);
        $this->setViewLayout('index');
    }

    /**
     * Default action
     *
     * @return void
     */
    public function indexAction()
    {
        if(SessionController::sessionAlive())
        {
            $this->setResponseRedirect(AB_Request::url("dashboard"));
        }
    }
}
