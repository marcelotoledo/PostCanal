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
    // TODO update this to new method (translation)
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
        $this->view->login = false;
        $this->view->message = $this->translation->login_invalid;

        /* check for existing profile */

        $profile = null;

        if(strlen($email) > 0 && strlen($password) > 0)
        {
            $profile = UserProfile::findByLogin($email, md5($password));
        }

        if(is_object($profile))
        {
            /* no register confirmation */

            if($profile->register_confirmation == false)
            {
                $this->view->message = $this->translation->register_unconfirmed;
            }

            /* valid login, create session */

            else
            {
                $this->session->setActive(true);
                $this->session->user_profile_id = $profile->user_profile_id;
                $this->session->user_profile_uid = $profile->uid;
                $this->session->user_profile_login_email = $profile->login_email;

                $profile->last_login_time = time();
                $profile->save();

                $this->view->login = true;
                $this->view->message = "";

                $id = $profile->user_profile_id;
                $_d = array ('method' => __METHOD__, 'user_profile_id' => $id);
                self::log("session created", $_d);
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
        $this->view->register = false;
        $this->view->message = $this->translation->register_invalid;

        /* check for existing profile */

        $profile = null;
        $information = null;

        if(strlen($email) > 0 && 
           strlen($password) > 0 && strlen($confirm) > 0 && $password == $confirm)
        {
            $profile = UserProfile::findByEmail($email);

            /* register new user profile */

            if(is_object($profile) == false)
            {
                $profile = new UserProfile();
                $profile->login_email = $email;
                $profile->login_password_md5 = md5($password);
                $profile->save();

                $id = intval($profile->user_profile_id);
                $_d = array ('method' => __METHOD__, 'user_profile_id' => $id);
                self::log("registered new", $_d);
            }
        }

        /* send instruction */

        if(is_object($profile))
        {
            try
            {
                if($profile->register_confirmation)
                {
                    $this->notify($profile, "register_existing");
                }
                else
                {
                    $this->notify($profile, "register_new");

                    if(is_object($information))
                    {
                        $information->register_message_time = time();
                        $information->save();
                    }
                }

                $this->view->register = true;
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
        $this->view->recovery = false;
        $this->view->message = $this->translation->recovery_failed;

        /* recovery instructions */

        if(is_object($profile))
        {
            try
            {
                $this->notify($profile, "recovery");
                $profile->recovery_message_time = time();
                $profile->save();
                $this->view->recovery = true;
                $this->view->message = $this->translation->recovery_sent;
            }
            catch(AB_Exception $exception)
            {
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
                $this->notify($email, "dummy");
                $this->view->recovery = true;
                $this->view->message = $this->translation->recovery_sent;
            }
            catch(AB_Exception $exception)
            {
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
        $this->view->accepted = false;
        $this->view->message = $this->translation->confirm_failed;

        $profile = null;

        if(strlen($email) > 0 && strlen($uid) > 0)
        {
            $profile = UserProfile::findByUID($email, $uid);
        }

        if(is_object($profile))
        {
            if($profile->register_confirmation == true)
            {
                $this->view->message = $this->translation->confirm_done_before;
            }
            else
            {
                $profile->uid = APP_Utility::randomString(8);
                $profile->register_confirmation = true;
                $profile->register_confirmation_time = time();
                $profile->save();

                $this->view->message = $this->translation->confirm_accepted;
            }

            $this->view->accepted = true;
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

        if(strlen($email) > 0 && strlen($uid) > 0)
        {
            $this->view->profile = UserProfile::findByUID($email, $uid);
        }
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
        $this->view->updated = false;
        $message = $this->translation->password_failed;

        /* password change (authenticated) */

        if(strlen($current) > 0 && ($id = intval($this->session->user_profile_id)) > 0)
        {
            $this->sessionAuthorize();
            $this->view->updated = $this->passwordAuthenticated
                ($id, $current, $password, $confirm, $message);
        }
 
        /* password change (not authenticated) */

        if(strlen($email) > 0 && strlen($uid) > 0 && 
           strlen($password) && strlen($confirm))
        {
            $this->view->updated = $this->passwordNotAuthenticated
                ($email, $uid, $password, $confirm, $message);
        }

        $this->view->message = $message;
    }

    /**
     * Online password change (authenticated)
     *
     * @param   integer $id         User profile ID
     * @param   string  $current    Current password
     * @param   string  $password
     * @param   string  $confirm
     * @param   string  $message
     * @return  boolean
     */
    private function passwordAuthenticated
        ($id, $current, $password, $confirm, &$message)
    {
        $updated = false;

        if(is_object($profile = $profile = UserProfile::findByPrimaryKey($id)))
        {
            if($password != $confirm)
            {
                $message = $this->translation->password_not_match;
            }
            elseif($profile->login_password_md5 != md5($current))
            {
                $message = $this->translation->password_invalid;
            }

            /* all ok ! */

            else
            {
                $profile->login_password_md5 = md5($password);
                $profile->save();

                $updated = true;
                $message = $this->translation->password_updated;

                $id = intval($profile->user_profile_id);
                $_d = array ('method' => __METHOD__, 'user_profile_id' => $id);
                self::log("password changed", $_d);
            }
        }

        return $updated;
    }

    /**
     * Offline password change (not authenticated)
     *
     * @param   string  $email
     * @param   string  $uid
     * @param   string  $password
     * @param   string  $confirm
     * @param   string  $message
     * @return  boolean
     */
    private function passwordNotAuthenticated
        ($email, $uid, $password, $confirm, &$message)
    {
        $updated = false;

        if(is_object($profile = $profile = UserProfile::findByUID($email, $uid)))
        {
            if($password != $confirm)
            {
                $message = $this->translation->password_not_match;
            }
            else
            {
                $profile->uid = APP_Utility::randomString(8);
                $profile->login_password_md5 = md5($password);
                $profile->save();

                $updated = true;
                $message = $this->translation->password_updated;

                $id = intval($profile->user_profile_id);
                $_d = array ('method' => __METHOD__, 'user_profile_id' => $id);
                self::log("password changed", $_d);
                # $this->notify($profile, "updated");
                $this->session->setActive(false);
            }
        }

        return $updated;
    }

    /**
     * Email update action
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
     * Email update form
     * 
     * @return  array
     */
    private function emailMethodGET()
    {
        $this->view->setLayout('index');

        $email = $this->request->email;
        $uid = $this->request->uid;
        $this->view->profile = null;

        if(strlen($email) > 0 && strlen($uid) > 0)
        {
            $this->view->profile = UserProfile::findByUID($email, $uid);
        }

        $this->view->profile = $profile;
    }

    /**
     * Email update request/save
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
        $this->view->accepted = false;
        $this->view->message = $this->translation->email_failed;
        $profile = null;

        /* change request (authenticated) */

        if(strlen($new_email) > 0 && ($id = intval($this->session->user_profile_id)) > 0)
        {
            $this->sessionAuthorize();
            $this->view->accepted = $this->emailChangeRequest
                ($id, $new_email, $this->view->message);
        }
        
        /* change save */

        if(strlen($email) > 0 && strlen($uid) > 0 && strlen($password) > 0)
        {
            $this->view->accepted = $this->emailChangeSave
                ($email, $uid, $password, $this->view->message);
        }
    }

    /**
     * Email change request
     * 
     * @param   integer $id         User profile ID
     * @param   string  $new_email  New user profile login email
     * @param   string  $message
     * @return  boolean
     */
    private function emailChangeRequest($id, $new_email, &$message)
    {
        $accepted = false;

        if(is_object($profile = UserProfile::findByPrimaryKey($id)))
        {
            if($profile->login_email == $new_email)
            {
                $message = $this->translation->email_unchanged;
            }
            else
            {
                try
                {
                    $profile->email_update = $new_email;
                    $profile->email_update_message_time = time();
                    $profile->save();
                    $this->notify($profile, "email_change");
                    $message = $this->translation->change_request_accepted;
                    $accepted = true;
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
        }

        return $accepted;
    }

    /**
     * Email change save
     * 
     * @param   string  $email      New user profile login email
     * @param   string  $uid        Profile UID
     * @param   string  $password   Profile password
     * @param   string  $message
     * @return  boolean
     */
    private function emailChangeSave($email, $uid, $password, &$message)
    {
        $accepted = false;

        if(is_object($profile = UserProfile::findByUID($email, $uid)))
        {
            if($profile->login_password_md5 != md5($password))
            {
                $message = $this->translation->email_unmatched_password;
            }
            else
            {
                if(strlen(($new_email = $profile->email_update)) > 0)
                {
                    $profile->login_email = $new_email;
                    $profile->uid = APP_Utility::randomString(8);
                    $profile->save();

                    $accepted = true;
                    $message = $this->translation->email_change_accepted;

                    $id = $profile->user_profile_id;
                    $_d = array ('method' => __METHOD__, 'user_profile_id' => $id);
                    self::log("email changed", $_d);
                    $this->notify($profile, "updated");
                }
            }
        }

        return $accepted;
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
        $this->view->profile = UserProfile::findByPrimaryKeyEnabled($id);

        if(is_object($this->view->profile) == false) 
        {
            $this->response->setRedirect(BASE_URL);
        }
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
        $this->view->saved = false;
        $this->view->message = $this->translation->edit_failed;

        if(!is_object($profile))
        {
            $_m = "invalid user profile";
            $_d = array('method' => __METHOD__, 'user_profile_id' => $id);
            throw new AB_Exception($_m, E_USER_WARNING, $_d);
        }

        $profile->name = $this->request->name;

        try
        {
            $profile->save();
            $this->view->saved = true;
            $this->view->message = $this->translation->edit_saved;
        }
        catch(AB_Exception $exception)
        {
            $_m = "failed to save information after editing";
            $_d = array('method' => __METHOD__, 'user_profile_id' => $id);
            AB_Exception::forward($_m, E_USER_WARNING, $exception, $_d);
        }
    }

    /**
     * Send notification mail
     *
     * @param   mixed       $recipient
     * @param   string      $template
     * @return  boolean
     */
    private function notify ($recipient, $template)
    {
        $mailer = new APP_Mailer();

        $subject = "mail_" . $template . "_subject";
        $mailer->setSubject($this->translation->{$subject});
        $template = $this->request->getController() . "/mail_" . $template;

        $profile = is_object($recipient) ? $recipient : null;

        ob_start();
        include AB_View::getTemplatePath($template);
        $mailer->setBody(ob_get_clean());

        $email = $profile ? $profile->login_email : $recipient;

        return $mailer->send($email, $template);
    }

    /**
     * Log user profile activities
     *
     * @param   string  $_m
     * @param   integer $_d
     * @return  void
     */
    private static function log($_m, $_d=array())
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
