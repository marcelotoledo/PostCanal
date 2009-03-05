<?php

/**
 * Abstract controller class
 * 
 * @category    Blotomate
 * @package     Controller
 * @author      Rafael Castilho <rafael@castilho.biz>
 */
abstract class AbstractController extends AB_Controller
{
    /**
     * Run controller action
     * 
     * @see AB_Controller::runAction
     */
    public function runAction($name)
    {
        /* load translation data */

        $this->translation->load($this->view->getTemplate());

        /* run parent action */

        try
        {
            parent::runAction($name);
        }
        catch(AB_Exception $exception)
        {
            /* add user profile information to exception */

            $id = intval($this->session->user_profile_id);
            $_m = "an error occurred during the execution of action (" . $name . ")";
            $_d = array('method' => __METHOD__);
            if($id > 0) $_d = array_merge($_d, array('user_profile_id' => $id));
            AB_Exception::forward($_m, E_USER_NOTICE, $exception, $_d);
        }
    }   
}
