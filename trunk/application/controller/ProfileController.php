<?php

/**
 * Profile controller class
 * 
 * @category    Autoblog
 * @package     Controller
 */
class ProfileController extends SessionController
{
    /**
     * Response constants
     */
    const LOGIN_OK = "login_ok";
    const LOGIN_INVALID = "login_invalid";
    const LOGIN_REGISTER_UNCONFIRMED = "login_register_unconfirmed";

    const REGISTER_OK = "register_ok";
    const REGISTER_FAILED = "register_failed";
    const REGISTER_INCOMPLETE = "register_incomplete";
    const REGISTER_PASSWORD_NOT_MATCHED = "register_password_not_matched";
    const REGISTER_INSTRUCTION_FAILED = "register_instruction_failed";

    const RECOVERY_OK = "recovery_ok";
    const RECOVERY_INSTRUCTION_FAILED = "recovery_instruction_failed";

    const CONFIRM_OK = "confirm_ok";
    const CONFIRM_FAILED = "confirm_failed";
    const CONFIRM_DONE_BEFORE = "confirm_done_before";

    const PASSWORD_CHANGE_OK = "password_change_ok";
    const PASSWORD_CHANGE_FAILED = "password_change_failed";
    const PASSWORD_CHANGE_NOT_MATCHED = "password_change_not_matched";

    const EDIT_SAVE_OK = "edit_save_ok";
    const EDIT_SAVE_FAILED = "edit_save_failed";
    const EDIT_SAVE_PASSWORD_NOT_MATCHED = "edit_save_password_not_matched";
    const EDIT_SAVE_WRONG_PASSWORD = "edit_save_wrong_password";

    /**
     * Mailer constants
     */
    const MAIL_NEW_PROFILE_SUBJECT = "[autoblog] new profile";
    const MAIL_NEW_PROFILE_TEMPLATE = "mail_register_new.html";
    const MAIL_EXISTING_PROFILE_SUBJECT = "[autoblog] existing profile";
    const MAIL_EXISTING_PROFILE_TEMPLATE = "mail_register_existing.html";
    const MAIL_RECOVERY_SUBJECT = "[autoblog] recuperar senha";
    const MAIL_RECOVERY_TEMPLATE = "mail_recovery.html";
    const MAIL_PASSWORD_SUBJECT = "[autoblog] senha alterada";
    const MAIL_PASSWORD_TEMPLATE = "mail_password.html";
    const MAIL_DUMMY_SUBJECT = "[autoblog] perfil inexistente";
    const MAIL_DUMMY_TEMPLATE = "mail_dummy.html";


    /**
     * Profile controller constructor
     *
     * @param   AB_Request  $request
     * @param   AB_Response $result
     * @return  void
     */
    public function __construct($request, $result)
    {
        parent::__construct($request, $result);
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
        $result = self::LOGIN_INVALID;

        /* check for existing profile */

        if(!empty($email) && !empty($password))
        {
            $profile = UserProfile::getFromLogin($email, md5($password));

            if(is_object($profile))
            {
                /* valid login, create session */

                if($profile->register_confirmation)
                {
                    $identification = array
                    (
                        'uid' => $profile->getUID(),
                        'label' => $profile->login_email,
                    );
                    $this->sessionCreate($identification);
                    $result = self::LOGIN_OK;
                }

                /* no register confirmation */

                else
                {
                    $result = self::LOGIN_REGISTER_UNCONFIRMED;
                }
            }
        }

        $this->getView()->setLayout(null);
        return Zend_Json::encode(array('result' => $result));
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
        $result = self::REGISTER_FAILED;

        /* check for existing profile */

        if(empty($email) || empty($password) || empty($confirm))
        {
            $result = self::REGISTER_INCOMPLETE;
        }
        elseif(!empty($password) && !empty($confirm) && $password != $confirm)
        {
            $result = self::REGISTER_PASSWORD_NOT_MATCHED;
        }
        else
        {
            $profile = UserProfile::getFromEmail($email);

            /* register new user profile */

            if(!is_object($profile))
            {
                $profile = new UserProfile();
                $profile->login_email = $email;
                $profile->login_password_md5 = md5($password);
                $profile->save();
            }
        }

        /* send instruction */

        if(is_object($profile))
        {
            try
            {
                if($profile->register_confirmation)
                {
                    self::sendExistingInstruction($profile);
                }
                else
                {
                    self::sendNewInstruction($profile);
                    $profile->register_message_time = date("Y-m-d H:i:s");
                    $profile->save();
                }

                $result = self::REGISTER_OK;
            }
            catch(Exception $exception)
            {
                $message = $exception->getMessage();
                AB_Log::write($message, AB_Log::PRIORITY_WARNING);

                /* disable unconfirmed profile 
                   when unable to send instructions */

                if(!$profile->register_confirmation)
                {
                    $profile->enabled = false;
                    $profile->save();
                }

                $result = self::REGISTER_INSTRUCTION_FAILED;
            }
        }

        $this->getView()->setLayout(null);
        return Zend_Json::encode(array('result' => $result));
    }

    /**
     * Logout
     *
     * @return  void
     */
    public function logoutAction()
    {
        $this->sessionDestroy();
        $this->getResponse()->setRedirect(BASE_URL);
    }

    /**
     * Password recovery
     *
     * @return  string
     */
    public function recoveryAction()
    {
        $email = $this->getRequest()->email;
        $profile = UserProfile::getFromEmail($email);
        $result = self::RECOVERY_OK;

        /* recovery instructions */

        if(is_object($profile))
        {
            try
            {
                self::sendRecoveryInstruction($profile);
                $profile->recovery_message_time = date("Y-m-d H:i:s");
                $profile->save();
            }
            catch(Exception $exception)
            {
                $message = $exception->getMessage();
                AB_Log::write($message, AB_Log::PRIORITY_ERROR);
                $result = self::RECOVERY_INSTRUCTION_FAILED;
            }
        }

        /* dummy instructions (not registered profile) */

        else
        {
            try
            {
                self::sendDummyInstruction($email);
            }
            catch(Exception $exception)
            {
                $message = $exception->getMessage();
                AB_Log::write($message, AB_Log::PRIORITY_WARNING);
                $result = self::RECOVERY_INSTRUCTION_FAILED;
            }
        }

        $this->getView()->setLayout(null);
        return Zend_Json::encode(array('result' => $result));
    }

    /**
     * Confirm register
     *
     * @return  array
     */
    public function confirmAction()
    {
        $uid = $this->getRequest()->uid;
        $profile = null;
        $result = self::CONFIRM_FAILED;

        if(!empty($uid))
        {
            $profile = UserProfile::getFromUID($uid);
        }

        if(is_object($profile))
        {
            if($profile->register_confirmation == false)
            {
                $profile->register_confirmation = true;
                $profile->register_confirmation_time = date("Y-m-d H:i:s");
                $profile->save();

                $result = self::CONFIRM_OK;
            }
            else
            {
                $result = self::CONFIRM_DONE_BEFORE;
            }
        }

        $this->getView()->setLayout('index');
        return array('result' => $result);
    }

    /**
     * Password change form
     * 
     * @return  array
     */
    public function passwordAction()
    {
        $uid = $this->getRequest()->uid;
        $password = $this->getRequest()->password;
        $confirm = $this->getRequest()->confirm;
        $profile = null;

        if(!empty($uid))
        {
            $profile = UserProfile::getFromUID($uid);
        }

        $this->getView()->setLayout('index');
        return array('profile' => $profile);
    }

    /**
     * Password change action
     * 
     * @return  string
     */
    public function passwordChangeAction()
    {
        $uid = $this->getRequest()->uid;
        $password = $this->getRequest()->password;
        $confirm = $this->getRequest()->confirm;
        $profile = null;
        $result = self::PASSWORD_CHANGE_FAILED;

        if(!empty($uid))
        {
            $profile = UserProfile::getFromUID($uid);
        }

        if(is_object($profile))
        {
            if(!empty($password) && !empty($confirm))
            {
                if($password == $confirm)
                {
                    $profile->login_password_md5 = md5($password);
                    $profile->save();
                    $result = self::PASSWORD_CHANGE_OK;
                    self::sendPasswordNotice($profile);
                    $this->sessionDestroy();
                }
                else
                {
                    $result = self::PASSWORD_CHANGE_NOT_MATCHED;
                }
            }
        } 

        $this->getView()->setLayout(null);
        return Zend_Json::encode(array('result' => $result));
    }

    /**
     * Profile editing form action
     *
     * @return array|null
     */
    public function editAction()
    {
        $uid = self::getUIDFromSession();
        $profile = null;

        if(!empty($uid))
        {
            $profile = UserProfile::getFromUID($uid);
        }

        if(empty($profile))
        {
            $this->getResponse()->setRedirect(BASE_URL);
            return null;
        }

        $id = $profile->user_profile_id;
        $information = UserInformation::getFromPrimaryKey($id);

        if(empty($information))
        {
            $information = new UserInformation();
        }

        $this->getView()->setLayout('dashboard');
        return array('information' => $information);
    }

    /**
     * Profile editing save action
     *
     * @return string
     */
    public function editSaveAction()
    {
        $pwdchange = $this->getRequest()->pwdchange;
        $name = $this->getRequest()->name;
        $current_password = $this->getRequest()->current_password;
        $new_password = $this->getRequest()->new_password;
        $new_password_confirm = $this->getRequest()->new_password_confirm;

        $uid = self::getUIDFromSession();
        $profile = null;
        $result = self::EDIT_SAVE_FAILED;

        $this->getView()->setLayout(null);

        if(!empty($uid))
        {
            $profile = UserProfile::getFromUID($uid);
        }

        if(empty($profile))
        {
            $this->getResponse()->setRedirect(BASE_URL);
            return Zend_Json::encode(array('result' => $result));
        }

        /* password change */

        if($pwdchange == "yes")
        {
            if($profile->login_password_md5 != md5($current_password))
            {
                $result = self::EDIT_SAVE_WRONG_PASSWORD;
                return Zend_Json::encode(array('result' => $result));
            }

            if($new_password != $new_password_confirm)
            {
                $result = self::EDIT_SAVE_PASSWORD_NOT_MATCHED;
                return Zend_Json::encode(array('result' => $result));
            }

            $profile->login_password_md5 = md5($new_password);

            $identification = array
            (
                'uid' => $profile->getUID(),
                'label' => $profile->login_email,
            );
            $this->sessionCreate($identification);

            try
            {
                $profile->save();
                $result = self::EDIT_SAVE_OK;
            }
            catch(Exception $exception)
            {
                $message = $exception->getMessage();
                AB_Log::write($message, AB_Log::PRIORITY_ERROR);
            }
        }

        /* information change */

        $id = $profile->user_profile_id;
        $information = UserInformation::getFromPrimaryKey($id);

        if(empty($information))
        {
            $information = new UserInformation();
            $information->user_profile_id = $id;
        }

        $information->name = $name;

        try
        {
            $information->save();
            $result = self::EDIT_SAVE_OK;
        }
        catch(Exception $exception)
        {
            $message = $exception->getMessage();
            AB_Log::write($message, AB_Log::PRIORITY_ERROR);
        }

        return Zend_Json::encode(array('result' => $result));
    }

    /**
     * Send email
     *
     * @param   string  $email
     * @param   string  $identifier
     * @param   string  $subject
     * @param   string  $body
     * @throws  Exception
     * @return  void
     */
    private static function sendEmail($email, $identifier, $subject, $body)
    {
        include APPLICATION_PATH . "/library/ApplicationMailer.php";

        $mailer = new ApplicationMailer();
        $mailer->setSubject($subject);
        $mailer->setBody($body);
        $mailer->send($email, $identifier);
    }

    /**
     * Send new profile instruction
     *
     * @param   UserProfile $profile
     * @throws  Exception
     * return   boolean
     */
    public static function sendNewInstruction($profile)
    {
        if(!is_object($profile))
        {
            return false;
        }

        $subject = self::MAIL_NEW_PROFILE_SUBJECT;
        $body = self::readInstruction(self::MAIL_NEW_PROFILE_TEMPLATE);

        $confirm_url = AB_Request::url("profile", 
                                       "confirm", 
                                       array("uid" => $profile->getUID()));

        $body = str_replace("{CONFIRM_URL}", $confirm_url, $body);

        self::sendEmail($profile->login_email, __METHOD__, $subject, $body);

        return true;
    }

    /**
     * Send existing profile instruction
     *
     * @param   UserProfile $profile
     * @throws  Exception
     * return   boolean
     */
    public static function sendExistingInstruction($profile)
    {
        if(!is_object($profile))
        {
            return false;
        }

        $subject = self::MAIL_EXISTING_PROFILE_SUBJECT;
        $body = self::readInstruction(self::MAIL_EXISTING_PROFILE_TEMPLATE);
            
        $body = str_replace("{BASE_URL}", BASE_URL, $body);

        $password_url = AB_Request::url("profile", 
                                        "password", 
                                        array("uid" => $profile->getUID()));

        $body = str_replace("{PASSWORD_URL}", $password_url, $body);

        self::sendEmail($profile->login_email, __METHOD__, $subject, $body);
 
        return true;
    }


    /**
     * Send recovery instruction
     *
     * @param   UserProfile $profile
     * @throws  Exception
     * return   boolean
     */
    public static function sendRecoveryInstruction($profile)
    {
        if(!is_object($profile))
        {
            return false;
        }

        $subject = self::MAIL_RECOVERY_SUBJECT;
        $body = self::readInstruction(self::MAIL_RECOVERY_TEMPLATE);

        $password_url = AB_Request::url("profile", 
                                        "password", 
                                        array("uid" => $profile->getUID()));

        $body = str_replace("{PASSWORD_URL}", $password_url, $body);

        self::sendEmail($profile->login_email, __METHOD__, $subject, $body);

        return true;
    }

    /**
     * Send recovery notice
     *
     * @param   UserProfile $profile
     * @throws  Exception
     * return   boolean
     */
    public static function sendPasswordNotice($profile)
    {
        if(!is_object($profile))
        {
            return false;
        }

        $subject = self::MAIL_PASSWORD_SUBJECT;
        $body = self::readInstruction(self::MAIL_PASSWORD_TEMPLATE);

        $body = str_replace("{BASE_URL}", BASE_URL, $body);

        self::sendEmail($profile->login_email, __METHOD__, $subject, $body);

        return true;
    }

    /**
     * Send dummy instruction
     *
     * @param   string      $email
     * @throws  Exception
     * return   boolean
     */
    public static function sendDummyInstruction($email)
    {
        if(empty($email))
        {
            return false;
        }

        $subject = self::MAIL_DUMMY_SUBJECT;
        $body = self::readInstruction(self::MAIL_DUMMY_TEMPLATE);

        $body = str_replace("{EMAIL}", $email, $body);
        $body = str_replace("{BASE_URL}", BASE_URL, $body);

        self::sendEmail($email, __METHOD__, $subject, $body);

        return true;
    }

    /**
     * Get UID from session
     *
     * @return  string|null
     */
    private static function getUIDFromSession()
    {
        $id = SessionController::getSessionIdentification();
        $uid = null;

        if(!empty($id))
        {
            if(is_array($id))
            {
                if(array_key_exists('uid', $id))
                {
                    $uid = $id['uid'];
                }
            }
        }

        return $uid;
    }

    /**
     * Read instruction template
     *
     * @param   string  $template
     * @return  string
     */
    private static function readInstruction($template)
    {
        $path = APPLICATION_PATH . "/view/template/Profile/" . $template;
        $body = "";

        if(file_exists($path))
        {
            $f = fopen($path, "r");
            while(!feof($f)) $body.= fgets($f);
            fclose($f);
        }

        return $body;
    }
}
