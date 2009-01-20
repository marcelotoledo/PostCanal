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
    const CONFIRM_DONE_BEFORE = "confirm_done_before";
    const CONFIRM_FAILED = "confirm_failed";

    const PASSWORD_SHOW_FORM = "password_show_form";
    const PASSWORD_CHANGE_NOT_MATCHED = "password_change_not_matched";
    const PASSWORD_CHANGE_OK = "password_change_ok";

    /**
     * Mailer constants
     */
    const MAIL_NEW_PROFILE_SUBJECT = "[autoblog] new profile";
    const MAIL_NEW_PROFILE_TEMPLATE = "mail_register_new.html";
    const MAIL_EXISTING_PROFILE_SUBJECT = "[autoblog] existing profile";
    const MAIL_EXISTING_PROFILE_TEMPLATE = "mail_register_existing.html";
    const MAIL_RECOVERY_SUBJECT = "[autoblog] recuperar senha";
    const MAIL_RECOVERY_TEMPLATE = "mail_recovery.html";
    const MAIL_DUMMY_SUBJECT = "[autoblog] perfil inexistente";
    const MAIL_DUMMY_TEMPLATE = "mail_dummy.html";


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

        if(!empty($email) && !empty($password))
        {
            $profile = UserProfile::getFromLogin($email, md5($password));

            if(is_object($profile))
            {
                /* valid login, create session */

                if($profile->register_confirmation)
                {
                    $this->sessionCreate($profile->getUID());
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
        $response = self::REGISTER_FAILED;

        /* check for existing profile */

        if(empty($email) || empty($password) || empty($confirm))
        {
            $response = self::REGISTER_INCOMPLETE;
        }
        elseif(!empty($password) && !empty($confirm) && $password != $confirm)
        {
            $response = self::REGISTER_PASSWORD_NOT_MATCHED;
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
                self::sendRegisterInstruction($profile);

                $profile->register_message_time = date("Y-m-d H:i:s");
                $profile->save();

                $response = self::REGISTER_OK;
            }
            catch(Exception $exception)
            {
                AB_Log::write($exception, AB_Log::PRIORITY_WARNING);

                /* disable when unable to send instructions */

                $profile->enabled = false;
                $profile->save();

                $response = self::REGISTER_INSTRUCTION_FAILED;
            }
        }

        return Zend_Json::encode(array('response' => $response));
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
        $response = self::RECOVERY_OK;

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
                AB_Log::write($exception, AB_Log::PRIORITY_ERROR);
                $response = self::RECOVERY_INSTRUCTION_FAILED;
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
                AB_Log::write($exception, AB_Log::PRIORITY_WARNING);
                $response = self::RECOVERY_INSTRUCTION_FAILED;
            }
        }

        return Zend_Json::encode(array('response' => $response));
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
        $response = self::CONFIRM_FAILED;

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

                $response = self::CONFIRM_OK;
            }
            else
            {
                $response = self::CONFIRM_DONE_BEFORE;
            }
        }

        $this->getView()->setLayout('main');
        return array('response' => $response);
    }

    /**
     * Change password
     * 
     * @return  array
     */
    public function passwordAction()
    {
        $uid = $this->getRequest()->uid;
        $password = $this->getRequest()->password;
        $confirm = $this->getRequest()->confirm;
        $profile = null;
        $response = self::PASSWORD_SHOW_FORM;

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
                    $response = self::PASSWORD_CHANGE_OK;
                }
                else
                {
                    $response = self::PASSWORD_CHANGE_NOT_MATCHED;
                }
            }
        } 

        if($response == self::PASSWORD_SHOW_FORM)
        {
            $this->getView()->setLayout('main');
            return array('response' => $response, 'profile' => $profile);
        }
        
        return Zend_Json::encode(array('response' => $response));
    }


    /**
     * Send register instruction
     *
     * @param   UserProfile $profile
     * @throws  Exception
     * return   boolean
     */
    public static function sendRegisterInstruction($profile)
    {
        if(!is_object($profile))
        {
            return false;
        }

        /* check message relay for last message time */

        if(!self::messageRelay($profile->register_message_time))
        {
            return false;
        }

        $registry = AB_Registry::singleton();

        $from_name = $registry->mailer->sender->from->name;
        $from_email = $registry->mailer->sender->from->email;

        $mail = new_Zend_Mail();
        $mail->setFrom($from_email, $from_name);
        $mail->addTo($profile->login_email);

        $subject = "";
        $body = "";

        /* existing profile instruction */
        
        if($profile->register_confirmation)
        {
            $subject = self::MAIL_EXISTING_PROFILE_SUBJECT;
            $body = self::readInstruction(self::MAIL_EXISTING_PROFILE_TEMPLATE);
            
            $body = str_replace("{BASE_URL}", BASE_URL, $body);

            $password_url = BASE_URL;
            $password_url.= "/profile/password?uid=" . $profile->getUID();
            $body = str_replace("{PASSWORD_URL}", $password_url, $body);
        }

        /* new profile instruction */

        else
        {
            $subject = self::MAIL_NEW_PROFILE_SUBJECT;
            $body = self::readInstruction(self::MAIL_NEW_PROFILE_TEMPLATE);

            $confirm_url = BASE_URL;
            $confirm_url.= "/profile/confirm?uid=" . $profile->getUID();
            $body = str_replace("{CONFIRM_URL}", $confirm_url, $body);
        }

        $mail->setSubject($subject);
        $mail->setBodyHtml($body);

        if(!empty($registry->mailer->transport))
        {
            $mail->send($registry->mailer->transport);
        }   

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

        /* check message relay for last message time */

        if(!self::messageRelay($profile->recovery_message_time))
        {
            return false;
        }

        $registry = AB_Registry::singleton();

        $from_name = $registry->mailer->sender->from->name;
        $from_email = $registry->mailer->sender->from->email;

        $mail = new_Zend_Mail();
        $mail->setFrom($from_email, $from_name);
        $mail->addTo($profile->login_email);

        $subject = self::MAIL_RECOVERY_SUBJECT;
        $body = self::readInstruction(self::MAIL_RECOVERY_TEMPLATE);

        $password_url = BASE_URL;
        $password_url.= "/profile/password?uid=" . $profile->getUID();
        $body = str_replace("{PASSWORD_URL}", $password_url, $body);

        $mail->setSubject($subject);
        $mail->setBodyHtml($body);

        if(!empty($registry->mailer->transport))
        {
            $mail->send($registry->mailer->transport);
        }   

        return true;
    }

    /**
     * Send dummy instruction
     *
     * @param   string|null  $email
     * @throws  Exception
     * return   boolean
     */
    public static function sendDummyInstruction($email=null)
    {
        if(empty($email))
        {
            return false;
        }

        /* check message relay for last message recipient */

        if(is_object(ApplicationDummyMessageRelay::getFromRecipient($email)))
        {
            return false;
        }

        $registry = AB_Registry::singleton();

        $from_name = $registry->mailer->sender->from->name;
        $from_email = $registry->mailer->sender->from->email;

        $mail = new_Zend_Mail();
        $mail->setFrom($from_email, $from_name);
        $mail->addTo($email);

        $subject = self::MAIL_DUMMY_SUBJECT;
        $body = self::readInstruction(self::MAIL_DUMMY_TEMPLATE);

        $body = str_replace("{EMAIL}", $email, $body);
        $body = str_replace("{BASE_URL}", BASE_URL, $body);

        $mail->setSubject($subject);
        $mail->setBodyHtml($body);

        if(!empty($registry->mailer->transport))
        {
            try
            {
                $mail->send($registry->mailer->transport);

                $relay = new ApplicationDummyMessageRelay();
                $relay->recipient = $email;
                $relay->save();
            }
            catch(Exception $exception)
            {
                throw new Exception($exception);
            }
        }   

        return true;
    }


    /**
     * Message time relay
     *
     * @param   integer $time   Last message timestamp
     * @return  boolean
     */
    private static function messageRelay($time)
    {
        $message_time = is_integer($time) ? $time : (int)strtotime($time);

        $registry = AB_Registry::singleton();

        $interval = $registry->mailer->sender->interval->minimum;

        if(empty($interval)) $interval = 0;

        return ((time() - $message_time) > $interval);
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
