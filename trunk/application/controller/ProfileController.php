<?php

/**
 * Profile controller class
 * 
 * @category    Blotomate
 * @package     Controller
 * @author      Rafael Castilho <rafael@castilho.biz>
 */
class ProfileController extends AbstractController
{
    const STATUS_OK = "ok";
    const STATUS_LOGGED = "logged";
    const STATUS_REGISTERED = "registered";
    const STATUS_FAILED = "failed";
    const STATUS_INVALID = "invalid";
    const STATUS_INCOMPLETE = "incomplete";
    const STATUS_UNCONFIRMED = "unconfirmed";
    const STATUS_WRONG_PASSWORD = "wrong_password";
    const STATUS_UNMATCHED_PASSWORD = "unmatched_password";
    const STATUS_INSTRUCTION_FAILED = "instruction_failed";

    const CONFIRM_OK = "confirm_ok";
    const CONFIRM_FAILED = "confirm_failed";
    const CONFIRM_DONE_BEFORE = "confirm_done_before";
    const CONFIRM_TYPE_NEW = "new";
    const CONFIRM_TYPE_CHANGE = "change";

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
        $result = self::STATUS_INVALID;

        /* check for existing profile */

        $profile = null;

        if(strlen($email) > 0 && strlen($password) > 0)
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

                $result = self::STATUS_LOGGED;

                $_d = array ('method' => __METHOD__,
                             'user_profile_id' => $this->user_profile_id);
                self::notice("session created", $_d);
            }

            /* no register confirmation */

            else
            {
                $result = self::STATUS_UNCONFIRMED;
            }
        }

        $this->setViewDataJson(compact(array('result')));
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
        $email = $this->getRequestParameter('email');
        $password = $this->getRequestParameter('password');
        $confirm = $this->getRequestParameter('confirm');
        $result = self::STATUS_FAILED;

        /* check for existing profile */

        $profile = null;

        if(strlen($email) == 0 || strlen($password) == 0 || strlen($confirm) == 0)
        {
            $result = self::STATUS_INCOMPLETE;
        }
        elseif(strlen($password) > 0 && strlen($confirm) > 0 && $password != $confirm)
        {
            $result = self::STATUS_UNMATCHED_PASSWORD;
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

                $id = intval($profile->user_profile_id);
                $_d = array ('method' => __METHOD__, 'user_profile_id' => $id);
                self::notice("registered new", $_d);
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

                $result = self::STATUS_REGISTERED;
            }
            catch(AB_Exception $exception)
            {
                /* disable unconfirmed profile */

                if(!$profile->register_confirmation)
                {
                    $profile->enabled = false;
                    $profile->save();
                }

                $id = intval($profile->user_profile_id);
                $_m = "failed to register;\n" . $exception->getMessage();
                $_d = array('method' => __METHOD__, 'user_profile_id' => $id);
                AB_Log::write($_m, $exception->getCode(), $_d);
            }
        }

        $this->setViewDataJson(compact(array('result')));
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
        $email = $this->getRequestParameter('email');
        $profile = UserProfile::findByEmail($email);
        $result = self::STATUS_OK;

        /* recovery instructions */

        if(is_object($profile))
        {
            try
            {
                self::sendRecoveryInstruction($profile);
                $profile->recovery_message_time = date("Y-m-d H:i:s");
                $profile->save();
            }
            catch(AB_Exception $exception)
            {
                $result = self::STATUS_FAILED;
                $id = intval($profile->user_profile_id);
                $_m = "failed to recovery;\n" . $exception->getMessage();
                $_d = array('method' => __METHOD__, 'user_profile_id' => $id);
                AB_Log::write($_m, $exception->getCode(), $_d);
            }
        }

        /* dummy instructions (not registered profile) */

        else
        {
            try
            {
                self::sendDummyInstruction($email);
            }
            catch(AB_Exception $exception)
            {
                $result = self::STATUS_FAILED;
                $_m = "failed to send dummy instructions to email (" . $email . ");\n";
                $_m.= $exception->getMessage();
                $_d = array('method' => __METHOD__);
                AB_Log::write($_m, $exception->getCode(), $_d);
            }
        }

        $this->setViewDataJson(compact(array('result')));
    }

    /**
     * Confirm register
     *
     * @return  array
     */
    public function confirmAction()
    {
        $this->setViewLayout('index');
        $uid = $this->getRequestParameter('uid');
        $type = $this->getRequestParameter('type');
        $result = self::CONFIRM_FAILED;

        $profile = null;

        if(!empty($uid))
        {
            $profile = UserProfile::findByUID($uid);
        }

        if(is_object($profile))
        {
            if($type == self::CONFIRM_TYPE_NEW)
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
            elseif($type == self::CONFIRM_TYPE_CHANGE) // TODO
            {
throw new UnexpectedValueException("see todo in (" . __FILE__ .":". __LINE__ . ")");
                if($profile->email_change == false)
                {
                    $profile->email_change = true;
                    $profile->email_change_time = date("Y-m-d H:i:s");
                    $profile->save();
                    $result = self::CONFIRM_OK;
                }
                else
                {
                    $result = self::CONFIRM_DONE_BEFORE;
                }
            }
        }

        $this->setViewParameter('result', $result);
    }

    /**
     * Password change action
     * 
     * @return  array
     */
    public function passwordAction()
    {
        $this->getRequestMethod() == AB_Request::METHOD_POST ?
            $this->passwordMethodPOST() :
            $this->passwordMethodGET();
    }

    /**
     * Password change form
     * 
     * @return  array
     */
    private function passwordMethodGET()
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
     * Password change save
     * 
     * @return void
     */
    private function passwordMethodPOST()
    {
        $this->setViewLayout(null);
        $this->setViewTemplate(null);
        $uid = $this->getRequestParameter('uid');
        $password = $this->getRequestParameter('password');
        $confirm = $this->getRequestParameter('confirm');
        $result = self::STATUS_FAILED;

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

                    $id = intval($profile->user_profile_id);
                    $_d = array ('method' => __METHOD__, 'user_profile_id' => $id);
                    self::notice("password changed", $_d);

                    $result = self::STATUS_OK;

                    self::sendPasswordNotice($profile);
                    $this->sessionDestroy();
                }
                else
                {
                    $result = self::STATUS_UNMATCHED_PASSWORD;
                }
            }
        }

        $this->setViewDataJson(compact(array('result')));
    }

    /**
     * Profile editing action
     *
     * @return array|null
     */
    public function editAction()
    {
        $this->getRequestMethod() == AB_Request::METHOD_POST ?
            $this->editMethodPOST() :
            $this->editMethodGET();
    }

    /**
     * Profile editing form
     *
     * @return array|null
     */
    private function editMethodGET()
    {
        $this->setViewLayout('dashboard');

        if(!$this->sessionAuthorize())
        {
            return null;
        }

        $id = intval($this->user_profile_id);
        $profile = UserProfile::findByPrimaryKeyEnabled($id);

        if(empty($profile)) 
        {
            $this->setResponseRedirect(BASE_URL);
            return null;
        }

        $information = UserProfileInformation::findByPrimaryKey($id);
        if(empty($information)) $information = new UserProfileInformation();

        $this->setViewParameter('profile', $profile);
        $this->setViewParameter('information', $information);
    }

    /**
     * Profile editing save
     *
     * @return void
     */
    private function editMethodPOST()
    {
        $this->setViewLayout(null);
        $this->setViewTemplate(null);
        $result = self::STATUS_FAILED;
        $id = 0;

        if(!self::sessionAlive())
        {
            $this->setResponseStatus(AB_Response::STATUS_UNAUTHORIZED);
            $_m = "session is not alive";
            $_d = array('method' => __METHOD__);
            throw new AB_Exception($_m, E_USER_NOTICE, $_d);
        }

        $id = intval($this->user_profile_id);
        $profile = ($id > 0) ? UserProfile::findByPrimaryKeyEnabled($id) : null;

        if(!is_object($profile))
        {
            $_m = "invalid user profile";
            $_d = array('method' => __METHOD__, 'user_profile_id' => $id);
            throw new AB_Exception($_m, E_USER_WARNING, $_d);
        }

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
                $result = self::STATUS_WRONG_PASSWORD;
                $this->setViewDataJson(compact(array('result')));
                return null;
            }

            if($new_password != $new_password_confirm)
            {
                $result = self::STATUS_UNMATCHED_PASSWORD;
                $this->setViewDataJson(compact(array('result')));
                return null;
            }

            $profile->login_password_md5 = md5($new_password);

            try
            {
                $profile->save();
                $_d = array ('method' => __METHOD__, 'user_profile_id' => $id);
                self::notice("password edited", $_d);

                /* regenerate session */

                $id = $profile->user_profile_id;
                $this->sessionDestroy();
                $this->sessionCreate();
                $this->user_profile_id = $id;
                $this->user_profile_uid = $profile->getUID();
                $this->user_profile_login_email = $profile->login_email;
                $this->sessionLock();

                $result = self::STATUS_OK;
            }
            catch(AB_Exception $exception)
            {
                $_m = "failed to save user profile after password editing";
                $_d = array('method' => __METHOD__, 'user_profile_id' => $id);
                AB_Exception::forward($_m, E_USER_WARNING, $exception, $_d);
            }
        }

        /* information change */

        $information = UserProfileInformation::findByPrimaryKey($id);

        if(empty($information))
        {
            $information = new UserProfileInformation();
            $information->user_profile_id = $id;
        }

        $information->name = $name;

        try
        {
            $information->save();
            $result = self::STATUS_OK;
        }
        catch(AB_Exception $exception)
        {
            $_m = "failed to save information after editing";
            $_d = array('method' => __METHOD__, 'user_profile_id' => $id);
            AB_Exception::forward($_m, E_USER_WARNING, $exception, $_d);
        }

        $this->setViewDataJson(compact(array('result')));
    }

    /**
     * Send email
     *
     * @param   string  $email
     * @param   string  $identifier
     * @param   string  $subject
     * @param   string  $body
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
     * @return  boolean
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
                "type" => self::CONFIRM_TYPE_NEW));

        $body = str_replace("{CONFIRM_URL}", $confirm_url, $body);

        self::sendEmail($profile->login_email, __METHOD__, $subject, $body);

        return true;
    }

    /**
     * Send existing profile instruction
     *
     * @param   UserProfile $profile
     * @return  boolean
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
     * @return  boolean
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
     * @return  boolean
     */
    public static function sendEmailChangeInstruction($profile)
    {
throw new BadMethodCallException("see todo in (" . __FILE__ .":". __LINE__ . ")");
        if(!is_object($profile))
        {
            return false;
        }

        $subject = self::MAIL_EMAIL_CHANGE_SUBJECT;
        $body = self::readInstruction(self::MAIL_EMAIL_CHANGE_TEMPLATE);

        $confirm_url = AB_Request::url(
            "profile", "confirm", array(
                "uid" => $profile->getUID(),
                "type" => self::CONFIRM_TYPE_CHANGE));

        $body = str_replace("{CONFIRM_URL}", $confirm_url, $body);

        self::sendEmail($profile->login_email_change, __METHOD__, $subject, $body);

        return true;
    }



    /**
     * Send recovery notice
     *
     * @param   UserProfile $profile
     * @return  boolean
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
     * @return  boolean
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
     * @return  void
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

    /**
     * Log user profile activities
     *
     * @param   string  $_m
     * @param   integer $_d
     * @return  void
     */
    private static function notice($_m, $_d=array())
    {
        $_m = $_m . " by ";

        foreach(array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR') as $i)
        {
            if(array_key_exists($i, $_SERVER))
            {
                $_m .= strtolower($i) . " (" . $_SERVER[$i] . ") ";
            }
        }

        AB_Log::write($_m, E_USER_NOTICE, $_d);
    }
}
