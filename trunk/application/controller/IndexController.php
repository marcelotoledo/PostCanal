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
