<?php

/**
 * Profile controller class
 * 
 * @category    Autoblog
 * @package     Controller
 */
class ProfileController extends AB_Controller
{
    /**
     * responses
     */
    const LOGIN_OK             = "login_ok";
    const LOGIN_INCOMPLETE     = "login_incomplete";
    const LOGIN_REGISTER_UNCONFIRMED = "login_register_unconfirmed";
    const LOGIN_INVALID        = "login_invalid";
    const LOGOUT_OK            = "logout_ok";
    const REGISTER_OK          = "register_ok";
    const REGISTER_INCOMPLETE  = "register_incomplete";
    const REGISTER_PASSWORD_UNCONFIRMED = "register_password_unconfirmed";
    const REGISTER_ERROR       = "register_error";


    /**
     * Profile controller constructor
     *
     * @param   AB_Request  $request
     * @param   AB_Response $response
     * @return  void
     */
    public function __construct($request, $response)
    {
        parent::__construct($request, $response);
        $this->getView()->setLayout(null);
    }

    /**
     * Login
     *
     * @return  string
     */
    public function loginAction()
    {
        $email = $this->getRequest()->email;
        $password = $this->getRequest()->password;

        $profile = null;
        $response = self::LOGIN_INVALID;

        /* check for existing profile */

        if(empty($email) || empty($password))
        {
            $response = self::LOGIN_INCOMPLETE;
        }
        else
        {
            $profile = UserProfile::checkLogin($email, md5($password));

            if(is_object($profile))
            {
                if($profile->register_confirmation)
                {
                    $this->sessionCreate($profile->login_email, 
                                         $profile->login_password_md5);

                    $response = self::LOGIN_OK;
                }

                /* no register confirmation */

                else
                {
                    $response = self::LOGIN_REGISTER_UNCONFIRMED;
                }
            }
        }

        return Zend_Json::encode(array('response' => $response));
    }

    /**
     * Register
     *
     * @return  string
     */
    public function registerAction()
    {
        $email = $this->getRequest()->email;
        $password = $this->getRequest()->password;
        $confirm = $this->getRequest()->confirm;

        $profile = null;
        $response = self::REGISTER_ERROR;

        /* check for existing profile */

        if(empty($email) || empty($password) || empty($confirm))
        {
            $response = self::REGISTER_INCOMPLETE;
        }
        elseif(!empty($password) && !empty($confirm) && $password != $confirm)
        {
            $response = self::REGISTER_PASSWORD_UNCONFIRMED;
        }
        else
        {
            $profile = UserProfile::checkEmail($email);

            /* register new user profile */

            if(!is_object($profile))
            {
                $profile = new UserProfile();
                $profile->login_email = $email;
                $profile->login_password_md5 = md5($password);
                $profile->save();
            }
        }

        /* send instructions */

        if(is_object($profile))
        {
            self::sendRegisterInstructions($profile);

            $response = self::REGISTER_OK;
        }

        return Zend_Json::encode(array('response' => $response));
    }

    /**
     * Logout
     *
     * @return  string
     */
    public function logoutAction()
    {
        $this->sessionDestroy();

        return Zend_Json::encode(array('response' => self::LOGOUT_OK));
    }

    /**
     * Send register instructions (TODO)
     *
     * @param   UserProfile $profile
     * return   boolean
     */
    public static function sendRegisterInstructions($profile)
    {
        if(!is_object($profile))
        {
            return false;
        }

        /* last register confirmation */

        $time_last_register = $profile->timeLastRegister();

        /* existing profile */
        
        if($profile->register_confirmation)
        {
        }

        /* new profile */

        else
        {
        }

        return false;
    }
}
