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
     * authentication responses
     */
    const AUTH_REG_OK  = "reg_ok";
    const AUTH_REG_ERR = "reg_err";
    const AUTH_CHK_OK  = "chk_ok";
    const AUTH_CHK_ERR = "chk_err";


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
        $this->getView()->setLayout('front');
    }

    /**
     * Default action
     *
     * @return void
     */
    public function indexAction()
    {
    }

    /**
     * Authentication
     *
     * @return  string
     */
    public function authenticationAction()
    {
        $register = $this->getRequest()->register == "yes" ? true : false;
        $email = $this->getRequest()->email;
        $password = $this->getRequest()->password;
        $confirm = $this->getRequest()->confirm;
        $profile = null;
        $response = null;


        /* check for existing profile */

        if(!empty($email))
        {
            $profile = UserProfile::checkEmail($email);
        }


        /* register new user profile */

        if($register)
        {
            $response = self::AUTH_REG_OK;

            if(empty($email) ||
               empty($password) ||
               empty($confirm) ||
               $password != $confirm) 
            {
                $response = self::AUTH_REG_ERR;
            }

            if(!is_object($profile) && $response == self::AUTH_REG_OK)
            {
                if(!empty($email) && !empty($password) &&
                   !empty($confirm) && $password == $confirm)
                {
                    $profile = new UserProfile();
                    $profile->login_email = $email;
                    $profile->login_password_md5 = md5($password);

                    if(!$profile->save()) $response = self::AUTH_REG_ERR;
                }
            }
            
            /* send instructions */

            if($response == self::AUTH_REG_OK)
            {
                self::sendRegisterInstructions($profile);
            }
        }


        /* authentication only */

        else
        {
            $response = self::AUTH_CHK_ERR;

            if(is_object($profile) && !empty($password))
                if($profile->login_password_md5 == md5($password))
                    $response = self::AUTH_CHK_OK;
        }

        $this->getView()->setLayout(null);
        return Zend_Json::encode(array('response' => $response));
    }

    /**
     * Send register instructions (TODO)
     *
     * @param   UserProfile $profile
     * return   boolean
     */
    private static function sendRegisterInstructions($profile)
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
