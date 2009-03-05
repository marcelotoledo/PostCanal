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
    const STATUS_FAILED = "failed";

    const CONFIRM_OK = "confirm_ok";
    const CONFIRM_FAILED = "confirm_failed";
    const CONFIRM_DONE_BEFORE = "confirm_done_before";

    const MAIL_NEW_PROFILE_SUBJECT = "[blotomate] novo perfil";
    const MAIL_NEW_PROFILE_TEMPLATE = "mail_register_new.html";
    const MAIL_EXISTING_PROFILE_SUBJECT = "[blotomate] perfil existente";
    const MAIL_EXISTING_PROFILE_TEMPLATE = "mail_register_existing.html";
    const MAIL_RECOVERY_SUBJECT = "[blotomate] recuperar senha";
    const MAIL_RECOVERY_TEMPLATE = "mail_recovery.html";
    const MAIL_PROFILE_SUBJECT = "[blotomate] perfil alterado";
    const MAIL_PROFILE_TEMPLATE = "mail_profile.html";
    const MAIL_DUMMY_SUBJECT = "[blotomate] perfil inexistente";
    const MAIL_DUMMY_TEMPLATE = "mail_dummy.html";
    const MAIL_EMAIL_CHANGE_SUBJECT = "[blotomate] mudança de e-mail";
    const MAIL_EMAIL_CHANGE_TEMPLATE = "mail_email_change.html";


    /**
     * Login
     *
     * @return  void
     */
    public function loginAction()
    {
        $this->response->setXML(true);

        $email = $this->request->email;
        $password = $this->request->password;
        $this->view->login = self::STATUS_FAILED;
        $this->view->message = $this->translation->login_invalid;

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
                $this->session->setActive(true);
                $this->session->user_profile_id = $profile->user_profile_id;
                $this->session->user_profile_uid = $profile->uid;
                $this->session->user_profile_login_email = $profile->login_email;

                $this->view->login = self::STATUS_OK;
                $this->view->message = '';

                $id = $this->session->user_profile_id;
                $information = UserProfileInformation::findByPrimaryKey($id);
                
                if(is_object($information))
                {
                    $information->last_login_time = time();
                    $information->save();
                }

                $_d = array ('method' => __METHOD__, 'user_profile_id' => $id);
                self::notice("session created", $_d);
            }

            /* no register confirmation */

            else
            {
                $this->view->result = self::STATUS_UNCONFIRMED;
                $this->view->message = $this->translation->register_unconfirmed;
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
        $this->response->setXML(true);

        $email = $this->request->email;
        $password = $this->request->password;
        $confirm = $this->request->confirm;
        $this->view->register = self::STATUS_FAILED;
        $this->view->message = $this->translation->register_invalid;

        /* check for existing profile */

        $profile = null;
        $information = null;

        if(strlen($email) > 0 && 
           strlen($password) > 0 && strlen($confirm) > 0 &&
           $password == $confirm)
        {
            $profile = UserProfile::findByEmail($email);

            /* register new user profile */

            if(is_object($profile))
            {
                $id = $profile->user_profile_id;
                $information = UserProfileInformation::findByPrimaryKey($id);
            }
            else
            {
                $profile = new UserProfile();
                $profile->login_email = $email;
                $profile->login_password_md5 = md5($password);
                $profile->save();

                $information = new UserProfileInformation();
                $information->user_profile_id = $profile->user_profile_id;
                $information->save();

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

                    if(is_object($information))
                    {
                        $information->register_message_time = time();
                        $information->save();
                    }
                }

                $this->view->register = self::STATUS_OK;
                $this->view->message = $this->translation->register_accepted;
            }
            catch(AB_Exception $exception)
            {
                $this->view->message = $this->translation->register_invalid_email;

                /* disable unconfirmed profile */

                if(!$profile->register_confirmation)
                {
                    $profile->enabled = false;
                    $profile->save();
                }

                $id = $profile->user_profile_id;
                $_m = "failed to register;\n" . $exception->getMessage();
                $_d = array('method' => __METHOD__, 'user_profile_id' => $id);
                AB_Log::write($_m, $exception->getCode(), $_d);
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
        $this->view->setLayout(null);
        $this->view->setTemplate(null);
        $this->session->setActive(false);
        $this->response->setRedirect(BASE_URL);
    }

    /**
     * Password recovery
     *
     * @return void
     */
    public function recoveryAction()
    {
        $this->response->setXML(true);

        $email = $this->request->email;
        $profile = UserProfile::findByEmail($email);
        $this->view->recovery = self::STATUS_FAILED;
        $this->view->message = $this->translation->recovery_failed;

        /* recovery instructions */

        if(is_object($profile))
        {
            try
            {
                self::sendRecoveryInstruction($profile);

                $id = $profile->user_profile_id;
                $information = UserProfileInformation::findByPrimaryKey($id);
                
                if(is_object($information))
                {
                    $information->recovery_message_time = time();
                    $information->save();
                }

                $this->view->recovery = self::STATUS_OK;
                $this->view->message = $this->translation->recovery_sent;
            }
            catch(AB_Exception $exception)
            {
                $this->view->message = $this->translation->recovery_failed;

                $id = $profile->user_profile_id;
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

                $this->view->recovery = self::STATUS_OK;
                $this->view->message = $this->translation->recovery_sent;
            }
            catch(AB_Exception $exception)
            {
                $this->view->result = self::STATUS_FAILED;

                $_m = "failed to send dummy instructions to email (" . $email . ");\n";
                $_m.= $exception->getMessage();
                $_d = array('method' => __METHOD__);
                AB_Log::write($_m, $exception->getCode(), $_d);
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
        $this->view->setLayout('index');

        $email = $this->request->email;
        $uid = $this->request->uid;
        $this->view->result = self::CONFIRM_FAILED;

        $profile = null;

        if(strlen($email) > 0 && strlen($uid) > 0)
        {
            $profile = UserProfile::findByUID($email, $uid);
        }

        if(is_object($profile))
        {
            if($profile->register_confirmation == false)
            {
                $profile->register_confirmation = true;
                $profile->save();

                $id = $profile->user_profile_id;
                $information = UserProfileInformation::findByPrimaryKey($id);

                if(is_object($information))
                {
                    $information->register_confirmation_time = time();
                    $information->save();
                }

                $this->view->result = self::CONFIRM_OK;
            }
            else
            {
                $this->view->result = self::CONFIRM_DONE_BEFORE;
            }
        }
    }

    /**
     * Password change action
     * 
     * @return  array
     */
    public function passwordAction()
    {
        $this->request->getMethod() == AB_Request::METHOD_POST ?
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
        $this->view->setLayout('index');

        $email = $this->request->email;
        $uid = $this->request->uid;
        $profile = null;

        if(strlen($email) > 0 && strlen($uid) > 0)
        {
            $profile = UserProfile::findByUID($email, $uid);
        }

        $this->view->profile = $profile;
    }

    /**
     * Password change save
     * 
     * @return void
     */
    private function passwordMethodPOST()
    {
        $this->response->setXML(true);

        $email = $this->request->email;
        $uid = $this->request->uid;
        $current = $this->request->current;
        $password = $this->request->password;
        $confirm = $this->request->confirm;
        $this->view->result = self::STATUS_FAILED;

        /* online password change (authenticated) */

        if(strlen($current) > 0 && ($id = intval($this->session->user_profile_id)) > 0)
        {
            $this->sessionAuthorize();
            $this->view->result = $this->passwordA($id, $current, $password, $confirm);
        }
 
        /* offline password change (not authenticated) */

        if(strlen($email) > 0 && strlen($uid) > 0 && 
           strlen($password) && strlen($confirm))
        {
            $this->view->result = $this->passwordN($email, $uid, $password, $confirm);
        }
    }

    /**
     * Online password change (authenticated)
     *
     * @param   integer $id         User profile ID
     * @param   string  $current    Current password
     * @param   string  $password
     * @param   string  $confirm
     * @return  string
     */
    private function passwordA($id, $current, $password, $confirm)
    {
        $result = self::STATUS_FAILED;

        if(is_object($profile = $profile = UserProfile::findByPrimaryKey($id)))
        {

            if($password != $confirm)
            {
                $result = self::STATUS_UNMATCHED_PASSWORD;
            }
            elseif($profile->login_password_md5 != md5($current))
            {
                $result = self::STATUS_WRONG_PASSWORD;
            }

            /* all ok ! */

            else
            {
                $profile->login_password_md5 = md5($password);
                $profile->save();

                $id = intval($profile->user_profile_id);
                $_d = array ('method' => __METHOD__, 'user_profile_id' => $id);
                self::notice("password changed", $_d);

                $result = self::STATUS_OK;
            }
        }

        return $result;
    }

    /**
     * Offline password change (not authenticated)
     *
     * @param   string  $email
     * @param   string  $uid
     * @param   string  $password
     * @param   string  $confirm
     * @return  string
     */
    private function passwordN($email, $uid, $password, $confirm)
    {
        $result = self::STATUS_FAILED;

        if(is_object($profile = $profile = UserProfile::findByUID($email, $uid)))
        {
            if($password == $confirm)
            {
                $profile->uid = APP_Utility::randomString(8);
                $profile->login_password_md5 = md5($password);
                $profile->save();

                $id = intval($profile->user_profile_id);
                $_d = array ('method' => __METHOD__, 'user_profile_id' => $id);
                self::notice("password changed", $_d);

                $result = self::STATUS_OK;

                self::sendProfileNotice($profile);
                $this->sessionDestroy();
            }
            else
            {
                $result = self::STATUS_UNMATCHED_PASSWORD;
            }
        }

        return $result;
    }

    /**
     * Email change action
     * 
     * @return  array
     */
    public function emailAction()
    {
        $this->request->getMethod() == AB_Request::METHOD_POST ?
            $this->emailMethodPOST() :
            $this->emailMethodGET();
    }

    /**
     * Email change form
     * 
     * @return  array
     */
    private function emailMethodGET()
    {
        $this->view->setLayout('index');

        $email = $this->request->email;
        $uid = $this->request->uid;
        $profile = null;
        $new_email = null;

        if(strlen($email) > 0 && strlen($uid) > 0)
        {
            $profile = UserProfile::findByUID($email, $uid);

            if(is_object($profile))
            {
                $id = $profile->user_profile_id;
                $information = UserProfileInformation::findByPrimaryKey($id);
                if(is_object($information)) $new_email = $information->email_update;
            }
        }

        $this->view->profile = $profile;
        $this->view->new_email = $new_email;
    }

    /**
     * Email change request/save
     * 
     * @return void
     */
    private function emailMethodPOST()
    {
        $this->response->setXML(true);

        $new_email = $this->request->new_email;
        $email = $this->request->email;
        $uid = $this->request->uid;
        $password = $this->request->password;
        $this->view->result = self::STATUS_FAILED;
        $profile = null;

        /* change request (authenticated) */

        if(strlen($new_email) > 0 && ($id = intval($this->session->user_profile_id)) > 0)
        {
            $this->sessionAuthorize();
            $this->view->result = $this->emailChangeRequest($id, $new_email);
        }
        
        /* change save */

        if(strlen($email) > 0 && strlen($uid) > 0 && strlen($password) > 0)
        {
            $this->view->result = $this->emailChangeSave($email, $uid, $password);
        }
    }

    /**
     * Email change request
     * 
     * @param   integer $id         User profile ID
     * @param   string  $new_email  New user profile login email
     * @return  string
     */
    private function emailChangeRequest($id, $new_email)
    {
        $result = self::STATUS_FAILED;

        if(is_object($profile = UserProfile::findByPrimaryKey($id)))
        {
            if($profile->login_email != $new_email)
            {
                try
                {
                    self::sendEmailUpdateInstruction($profile, $new_email);

                    $information = UserProfileInformation::findByPrimaryKey($id);
        
                    if(is_object($information))
                    {
                        $information->email_update = $new_email;
                        $information->email_update_message_time = time();
                        $information->save();
                        }

                    $result = self::STATUS_OK;
                }
                catch(AB_Exception $exception)
                {
                    $_m = "failed to send email change instructions " . 
                          "to email (" . $new_email . ");\n";
                    $_m.= $exception->getMessage();
                    $_d = array('method' => __METHOD__);
                    AB_Log::write($_m, $exception->getCode(), $_d);
                }
            }
            else
            {
                $result = self::STATUS_UNCHANGED_EMAIL;
            }
        }

        return $result;
    }

    /**
     * Email change save
     * 
     * @param   string  $email      New user profile login email
     * @param   string  $uid        Profile UID
     * @param   string  $password   Profile password
     * @return  string
     */
    private function emailChangeSave($email, $uid, $password)
    {
        $result = self::STATUS_FAILED;

        if(is_object($profile = UserProfile::findByUID($email, $uid)))
        {
            if($profile->login_password_md5 == md5($password))
            {
                $id = $profile->user_profile_id;
                $information = UserProfileInformation::findByPrimaryKey($id);

                if(is_object($information))
                {
                    if(strlen(($new_email = $information->email_update)) > 0)
                    {
                        $profile->login_email = $new_email;
                        $profile->uid = APP_Utility::randomString(8);
                        $profile->save();

                        $result = self::STATUS_OK;

                        $id = $profile->user_profile_id;
                        $_d = array ('method' => __METHOD__, 'user_profile_id' => $id);
                        self::notice("email changed", $_d);

                        self::sendProfileNotice($profile);
                    }
                }
            }
            else
            {
                $result = self::STATUS_UNMATCHED_PASSWORD;
            }
        }

        return $result;
    }

    /**
     * Profile editing action
     *
     * @return array|null
     */
    public function editAction()
    {
        $this->sessionAuthorize();

        $this->request->getMethod() == AB_Request::METHOD_POST ?
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
        $this->view->setLayout('dashboard');
        
        $id = intval($this->session->user_profile_id);
        $profile = UserProfile::findByPrimaryKeyEnabled($id);

        if(empty($profile)) 
        {
            $this->response->setRedirect(BASE_URL);
            return null;
        }

        $information = UserProfileInformation::findByPrimaryKey($id);
        if(!is_object($information)) $information = new UserProfileInformation();

        $this->view->profile = $profile;
        $this->view->information = $information;
    }

    /**
     * Profile editing save
     *
     * @return void
     */
    private function editMethodPOST()
    {
        $this->response->setXML(true);

        $id = intval($this->session->user_profile_id);
        $profile = ($id > 0) ? UserProfile::findByPrimaryKeyEnabled($id) : null;
        $this->view->result = self::STATUS_FAILED;

        if(!is_object($profile))
        {
            $_m = "invalid user profile";
            $_d = array('method' => __METHOD__, 'user_profile_id' => $id);
            throw new AB_Exception($_m, E_USER_WARNING, $_d);
        }

        $name = $this->request->name;

        $information = UserProfileInformation::findByPrimaryKey($id);

        if(!is_object($information))
        {
            $information = new UserProfileInformation();
            $information->user_profile_id = $id;
        }

        $information->name = $name;

        try
        {
            $information->save();
            $this->view->result = self::STATUS_OK;
        }
        catch(AB_Exception $exception)
        {
            $_m = "failed to save information after editing";
            $_d = array('method' => __METHOD__, 'user_profile_id' => $id);
            AB_Exception::forward($_m, E_USER_WARNING, $exception, $_d);
        }
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
        $mailer = new APP_Mailer();
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
                "email" => $profile->login_email,
                "uid" => $profile->uid));

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

        $password_url = AB_Request::url(
            "profile", "password", array(
                "email" => $profile->login_email, "uid" => $profile->uid));

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

        $password_url = AB_Request::url(
            "profile", "password", array(
                "email" => $profile->login_email, "uid" => $profile->uid));
       
        $body = str_replace("{PASSWORD_URL}", $password_url, $body);

        self::sendEmail($profile->login_email, __METHOD__, $subject, $body);

        return true;
    }

    /**
     * Send email update instruction
     *
     * @param   UserProfile $profile
     * @param   string      $email
     * @return  boolean
     */
    public static function sendEmailUpdateInstruction($profile, $email)
    {
        if(!is_object($profile)) return false;

        $subject = self::MAIL_EMAIL_CHANGE_SUBJECT;
        $body = self::readInstruction(self::MAIL_EMAIL_CHANGE_TEMPLATE);

        $email_url = AB_Request::url(
            "profile", "email", array(
                "email" => $profile->login_email,
                "uid" => $profile->uid));

        $body = str_replace("{EMAIL_URL}", $email_url, $body);

        self::sendEmail($email, __METHOD__, $subject, $body);

        return true;
    }



    /**
     * Send recovery notice
     *
     * @param   UserProfile $profile
     * @return  boolean
     */
    public static function sendProfileNotice($profile)
    {
        if(!is_object($profile))
        {
            return false;
        }

        $subject = self::MAIL_PROFILE_SUBJECT;
        $body = self::readInstruction(self::MAIL_PROFILE_TEMPLATE);

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
