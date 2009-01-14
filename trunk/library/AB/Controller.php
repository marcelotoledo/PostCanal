<?php

/**
 * Abstract controller
 * 
 * @category    Autoblog
 * @package     AB
 */
abstract class AB_Controller
{
    /**
     * Session check mode
     */
    const SESSION_CHECK_SIMPLE     = 1;
    const SESSION_CHECK_PERSISTENT = 2;


    /**
     * Request
     *
     * @var AB_Request
     */
    private $request;

    /**
     * Response
     *
     * @var AB_Response
     */
    private $response;

    /**
     * View
     * 
     * @var AB_View
     */
    private $view;


    /**
     * Controller constructor
     *
     * @param   AB_Request  $request
     * @param   AB_Response $response
     * @return  void
     */
    public function __construct($request, $response)
    {
        $this->request = $request;
        $this->response = $response;
        $this->view = new AB_View($request);
    }

    /**
     * Current request
     *
     * @return  AB_Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Current response
     *
     * @return  AB_Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Current view
     *
     * @return  AB_View
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * Run controller action
     *
     * @param   string      $name   Action name
     * @throws  Exception
     * @return  void
     */
    public function runAction($name=null)
    {
        $action = $name ? $name : $this->request->getAction();
        $action_method = $action . "Action";

        if(is_callable(array($this, $action_method)) == true)
        {
            $this->view->setData($this->{$action_method}());

            ob_start();
            $this->view->render();
            $this->response->setBody(ob_get_clean());
        }
        else
        {
            $this->response->setStatus(404);
            throw new Exception ("action " . $action . " not found");
        }
    }

    /**
     * Create login session
     *
     * @param   string  $username
     * @param   string  $password
     * @return  void
     */
    public function sessionCreate ($username, $password)
    {
        $registry = AB_Registry::singleton();
        $namespace = $registry->session->namespace;
        $expiration = $registry->session->expiration;

        $login = new Zend_Session_Namespace($namespace, true);
        $login->username = $username;
        $login->password = $password;
        $login->setExpirationSeconds($expiration);
        $login->lock();
    }

    /**
     * Check login session
     *
     * @return  void
     */
    public function sessionCheck ()
    {
        $registry = AB_Registry::singleton();
        $namespace = $registry->session->namespace;
        $check = $registry->session->check;
        $result = null;

        $login = new Zend_Session_Namespace($namespace, true);

        if(is_object($login))
        {
            $username = $login->username;
            $password = $login->password;
        }

        if($check->mode == self::SESSION_CHECK_PERSISTENT)
        {
            if(!empty($login->username) && !empty($login->password))
            {
                if(method_exists($check->class, $check->method))
                {
                    $result = call_user_func(array($check->class, 
                                                   $check->method,
                                                   $login->username,
                                                   $login->password));
                #$check = $classname::$methodname($login->username, 
                #                                 $login->password);
                }
            }
        }
        else
        {
            if(!empty($login->username))
            {
                $result = true;
            }
        }

        if(empty($result))
        {
            $this->response->setRedirect(
                $check->redirect, AB_Response::STATUS_UNAUTHORIZED);
        }
    }

    /**
     * Destroy login session
     *
     * @param   string  $login
     * @param   string  $password
     * @return  void
     */
    function sessionDestroy ()
    {
        $registry = AB_Registry::singleton();
        $namespace = $registry->session->namespace;

        $login = new Zend_Session_Namespace($namespace, true);
        $login->unLock();
        $login->unsetAll();
    }
}
