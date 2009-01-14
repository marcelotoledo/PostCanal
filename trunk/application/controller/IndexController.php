<?php

/**
 * Index controller class
 * 
 * @category    Autoblog
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
        $this->getView()->setLayout('main');
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
