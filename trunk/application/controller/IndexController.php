<?php

/**
 * Index controller class
 * 
 * @category    Blotomate
 * @package     Controller
 * @author      Rafael Castilho <rafael@castilho.biz>
 */
class IndexController extends AbstractController
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
        $this->view->setLayout('index');
    }

    /**
     * Default action
     *
     * @return void
     */
    public function indexAction()
    {
        if($this->session->getActive() == true)
        {
            $this->response->setRedirect(AB_Request::url("dashboard"));
        }
    }
}
