<?php

/**
 * Profile controller class
 * 
 * @category    Blotomate
 * @package     Controller
 */
class ProfileController extends SessionController
{
    /**
     * Request/Response constants
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
    const CONFIRM_TYPE_NEW_PROFILE = "newprofile";
    const CONFIRM_TYPE_EMAIL_CHANGE = "emailchange";

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
    const MAIL_NEW_PROFILE_SUBJECT = "[blotomate] novo perfil";
    const MAIL_NEW_PROFILE_TEMPLATE = "mail_register_new.html";
    const MAIL_EXISTING_PROFILE_SUBJECT = "[blotomate] perfil existente";
    const MAIL_EXISTING_PROFILE_TEMPLATE = "mail_register_existing.html";
    const MAIL_RECOVERY_SUBJECT = "[blotomate] recuperar senha";
    const MAIL_RECOVERY_TEMPLATE = "mail_recovery.html";
    const MAIL_PASSWORD_SUBJECT = "[blotomate] senha alterada";
    const MAIL_PASSWORD_TEMPLATE = "mail_password.html";
    const MAIL_DUMMY_SUBJECT = "[blotomate] perfil inexistente";
    const MAIL_DUMMY_TEMPLATE = "mail_dummy.html";
    const MAIL_EMAIL_CHANGE_SUBJECT = "[blotomate] mudança de e-mail";
    const MAIL_EMAIL_CHANGE_TEMPLATE = "mail_email_change.html";


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
    }

    /**
     * Login
     *
     * @return  void
     */
    public function loginAction()
    {
        $this->setViewLayout(null);
        $this->setViewTemplate(null);

        $email = $this->getRequestParameter('email');
        $password = $this->getRequestParameter('password');

        $this->setViewData(self::LOGIN_INVALID);

        /* check for existing profile */

        $profile = null;

        if(!empty($email) && !empty($password))
        {
            $profile = UserProfile::findByLogin($email, md5($password));
        }

        if(is_object($profile))
        {
            /* valid login, create session */

            if($profile->register_confirmation)
            {
                $this->sessionCreate();
                $this->user_profile_id = $profile->user_profile_id;
                $this->user_profile_uid = $profile->getUID();
                $this->user_profile_login_email = $profile->login_email;
                $this->sessionLock();

                $this->setViewData(self::LOGIN_OK);
            }

            /* no register confirmation */

            else
            {
                $this->setViewData(self::LOGIN_REGISTER_UNCONFIRMED);
            }
        }
    }

    /**
     * Register
     *
     * @return void
     */
    public function registerAction()
    {
        $this->setViewLayout(null);
        $this->setViewTemplate(null);
        $this->setViewData(self::REGISTER_FAILED);

        $email = $this->getRequestParameter('email');
        $password = $this->getRequestParameter('password');
        $confirm = $this->getRequestParameter('confirm');

        /* check for existing profile */

        $profile = null;

        if(empty($email) || empty($password) || empty($confirm))
        {
            $this->setViewData(self::REGISTER_INCOMPLETE);
        }
        elseif(!empty($password) && !empty($confirm) && $password != $confirm)
        {
            $this->setViewData(self::REGISTER_PASSWORD_NOT_MATCHED);
        }
        else
        {
            $profile = UserProfile::findByEmail($email);

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

                $this->setViewData(self::REGISTER_OK);
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

                $this->setViewData(self::REGISTER_INSTRUCTION_FAILED);
            }
        }
    }

    /**
     * Logout
     *
     * @return  void
     */
    public function logoutAction()
    {
        $this->setViewLayout(null);
        $this->setViewTemplate(null);
        $this->sessionDestroy();
        $this->setResponseRedirect(BASE_URL);
    }

    /**
     * Password recovery
     *
     * @return void
     */
    public function recoveryAction()
    {
        $this->setViewLayout(null);
        $this->setViewTemplate(null);
        $this->setViewData(self::RECOVERY_OK);

        $email = $this->getRequestParameter('email');
        $profile = UserProfile::findByEmail($email);

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
                $this->setViewData(self::RECOVERY_INSTRUCTION_FAILED);
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
                $this->setViewData(self::RECOVERY_INSTRUCTION_FAILED);
            }
        }
    }

    /**
     * Confirm register
     *
     * @return  array
     */
    public function confirmAction()
    {
        $this->setViewLayout('index');
        $this->setViewParameter('result', self::CONFIRM_FAILED);

        $uid = $this->getRequestParameter('uid');
        $type = $this->getRequestParameter('type');

        $profile = null;

        if(!empty($uid))
        {
            $profile = UserProfile::findByUID($uid);
        }

        if(is_object($profile))
        {
            if($type == self::CONFIRM_TYPE_NEW_PROFILE)
            {
                if($profile->register_confirmation == false)
                {
                    $profile->register_confirmation = true;
                    $profile->register_confirmation_time = date("Y-m-d H:i:s");
                    $profile->save();
    
                    $this->setViewParameter('result', self::CONFIRM_OK);
                }
                else
                {
                    $this->setViewParameter('result', 
                        self::CONFIRM_DONE_BEFORE);
                }
            }
            elseif($type == self::CONFIRM_TYPE_EMAIL_CHANGE) // TODO
            {
throw new UnexpectedValueException("see 'TODO' in " . __FILE__ .":". __LINE__);
                if($profile->email_change == false)
                {
                    $profile->email_change = true;
                    $profile->email_change_time = date("Y-m-d H:i:s");
                    $profile->save();
    
                    $this->setViewParameter('result', self::CONFIRM_OK);
                }
                else
                {
                    $this->setViewParameter('result', 
                        self::CONFIRM_DONE_BEFORE);
                }
            }
        }
    }

    /**
     * Password change form
     * 
     * @return  array
     */
    public function passwordAction()
    {
        $this->setViewLayout('index');

        $uid = $this->getRequestParameter('uid');
        $password = $this->getRequestParameter('password');
        $confirm = $this->getRequestParameter('confirm');

        $profile = null;

        if(!empty($uid))
        {
            $profile = UserProfile::findByUID($uid);
        }

        $this->setViewParameter('profile', $profile);
    }

    /**
     * Password change action
     * 
     * @return void
     */
    public function passwordChangeAction()
    {
        $this->setViewLayout(null);
        $this->setViewTemplate(null);
        $this->setViewData(self::PASSWORD_CHANGE_FAILED);

        $uid = $this->getRequestParameter('uid');
        $password = $this->getRequestParameter('password');
        $confirm = $this->getRequestParameter('confirm');

        $profile = null;

        if(!empty($uid))
        {
            $profile = UserProfile::findByUID($uid);
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
                    $this->setViewData(self::PASSWORD_CHANGE_NOT_MATCHED);
                }
            }
        } 
    }

    /**
     * Profile editing form action
     *
     * @return array|null
     */
    public function editAction()
    {
        $this->setViewLayout('dashboard');

        if(!$this->sessionAuthorize())
        {
            return null;
        }

        $id = $this->user_profile_id;
        $profile = UserProfile::findByPrimaryKey($id);

        if(empty($profile)) 
        {
            $this->setResponseRedirect(BASE_URL);
            return null;
        }

        $information = UserInformation::findByPrimaryKey($id);

        if(empty($information))
        {
            $information = new UserInformation();
        }

        $this->setViewParameter('profile', $profile);
        $this->setViewParameter('information', $information);
    }

    /**
     * Profile editing save action
     *
     * @return void
     */
    public function editSaveAction()
    {
        $this->setViewLayout(null);
        $this->setViewTemplate(null);
        $this->setViewData(self::EDIT_SAVE_FAILED);

        if(!self::sessionAlive())
        {
            throw new Exception("session is not alive");
        }

        $profile_id = $this->user_profile_id;
        $profile = UserProfile::findByPrimaryKey($profile_id);

        if(empty($profile)) throw new InvalidArgumentException();

        $pwdchange = $this->getRequestParameter('pwdchange');
        $name = $this->getRequestParameter('name');
        $current_password = $this->getRequestParameter('current_password');
        $new_password = $this->getRequestParameter('new_password');
        $new_password_confirm = $this->getRequestParameter('new_password_confirm');

        /* password change */

        if($pwdchange == "yes")
        {
            if($profile->login_password_md5 != md5($current_password))
            {
                $this->setViewData(self::EDIT_SAVE_WRONG_PASSWORD);
                return null;
            }

            if($new_password != $new_password_confirm)
            {
                $this->setViewData(self::EDIT_SAVE_PASSWORD_NOT_MATCHED);
                return null;
            }

            $profile->login_password_md5 = md5($new_password);

            try
            {
                $profile->save();

                /* regenerate session */

                $profile_id = $profile->user_profile_id;
                $this->sessionDestroy();
                $this->sessionCreate();
                $this->user_profile_id = $profile_id;
                $this->user_profile_uid = $profile->getUID();
                $this->user_profile_login_email = $profile->login_email;
                $this->sessionLock();

                $this->setViewData(self::EDIT_SAVE_OK);
            }
            catch(Exception $exception)
            {
                $message = $exception->getMessage();
                AB_Log::write($message, AB_Log::PRIORITY_ERROR);
            }
        }

        /* information change */

        $information = UserInformation::findByPrimaryKey($profile_id);

        if(empty($information))
        {
            $information = new UserInformation();
            $information->user_profile_id = $profile_id;
        }

        $information->name = $name;

        try
        {
            $information->save();
            $this->setViewData(self::EDIT_SAVE_OK);
        }
        catch(Exception $exception)
        {
            $message = $exception->getMessage();
            AB_Log::write($message, AB_Log::PRIORITY_ERROR);
        }
    }

    /**
     * Email change action (TODO)
     * 
     * @return void
     */
    public function emailChangeAction()
    {
throw new BadMethodCallException("see 'TODO' in " . __FILE__ .":". __LINE__);
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

        $confirm_url = AB_Request::url(
            "profile", "confirm", array(
                "uid" => $profile->getUID(),
                "type" => self::CONFIRM_TYPE_NEW_PROFILE));

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
     * Send email change instruction (TODO)
     *
     * @param   UserProfile $profile
     * @throws  Exception
     * return   boolean
     */
    public static function sendEmailChangeInstruction($profile)
    {
throw new BadMethodCallException("see 'TODO' in " . __FILE__ .":". __LINE__);
        if(!is_object($profile))
        {
            return false;
        }

        $subject = self::MAIL_EMAIL_CHANGE_SUBJECT;
        $body = self::readInstruction(self::MAIL_EMAIL_CHANGE_TEMPLATE);

        $confirm_url = AB_Request::url(
            "profile", "confirm", array(
                "uid" => $profile->getUID(),
                "type" => self::CONFIRM_TYPE_EMAIL_CHANGE));

        $body = str_replace("{CONFIRM_URL}", $confirm_url, $body);

        self::sendEmail($profile->login_email_change, __METHOD__, $subject, $body);

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
     * Read instruction template
     *
     * @param   string  $template
     * @return void
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
