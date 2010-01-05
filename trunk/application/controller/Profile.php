<?php

/**
 * Profile controller class
 * 
 * @category    PostCanal
 * @package     Application Controller
 * @author      Rafael Castilho <rafael@castilho.biz>
 */

class C_Profile extends B_Controller
{
    /**
     * Login
     *
     * @return  void
     */
    public function A_login()
    {
        $this->response()->setXML(true);

        $email = $this->request()->email;
        $password = $this->request()->password;
        $this->view()->login = false;
        $this->view()->message = $this->translation()->invalid_authentication;

        /* check for existing profile */

        $profile = null;

        if(strpos($email, '@') > 0 && strlen($password) > 0)
        {
            $profile = UserProfile::getByLogin($email, md5($password));
        }

        if(is_object($profile))
        {
            $this->session()->setActive(true);
            $this->session()->setCulture($profile->local_culture);
            $this->session()->setTimezone($profile->local_timezone);
            $this->session()->user_profile_id = $profile->user_profile_id;
            $this->session()->user_profile_hash = $profile->hash;
            $this->session()->user_profile_login_email = $profile->login_email;
            $this->session()->user_profile_register_confirmation = $profile->register_confirmation;

            $this->session()->user_profile_quota_blog = $profile->quota_blog;
            $this->session()->user_profile_quota_feed = $profile->quota_feed;
            $this->session()->user_profile_quota_publication_period = $profile->quota_publication_period;

            $profile->last_login_time = time();
            $profile->save();

            $this->view()->login = true;
            $this->view()->message = "";

            $id = $profile->user_profile_id;
            $_d = array ('method' => __METHOD__, 'user_profile_id' => $id);
            self::log("session created", $_d);
        }
    }

    /**
     * Register
     *
     * @return void
     */
    public function A_register()
    {
        $this->response()->setXML(true);

        $email = $this->request()->email;
        $password = $this->request()->password;
        $confirm = $this->request()->passwordc;
        $name = $this->request()->name;
        //$territory = $this->request()->country;
        //$timezone = $this->request()->timezone;
        $territory = 'US';
        $timezone = 'UTC'; // defaults

        $this->view()->register = false;
        $this->view()->rerecaptcha = false;
        $this->view()->message = $this->translation()->registration_invalid;

        /* check recaptcha */

        if(self::checkRecaptcha($_POST['recaptcha_challenge'],
                                $_POST['recaptcha_response'])==false)
        {
            $this->view()->message = "Please, check the two confirmation words.";
            $this->view()->rerecaptcha = true;
            return false;
        }

        /* check for invitation */

        if(B_Registry::get('application/profile/invitationOnly')=='true')
        {
            if(is_object($i = ProfileInvitation::getByEmail($email)) &&
                         $i->enabled==true)
            {
                B_Log::write(sprintf('new registration from (%s) with enabled invitation', $email), E_NOTICE, array('method' => __METHOD__));
            }
            else
            {
                B_Log::write(sprintf('new registration from (%s) blocked without invitation', $email), E_WARNING, array('method' => __METHOD__));
                $this->view()->message = "This email was not accepted for registration.";
                return false;
            }
        }

        /* check for existing profile */

        $profile = null;

        if(strpos($email, '@') > 0 && 
           strlen($password) > 0 && strlen($confirm) > 0 && $password == $confirm)
        {
            $profile = UserProfile::getByEmail($email);

            /* register new user profile */

            if(is_object($profile) == false)
            {
                $profile = new UserProfile();
                $profile->login_email = $email;
                $profile->update_email_to = $email;
                $profile->login_password_md5 = md5($password);
                $profile->name = $name;
                $profile->local_territory = $territory;
                $profile->local_timezone = $timezone;

                $profile->quota_blog = B_Registry::get('application/profile/quotaBlog');
                $profile->quota_feed = B_Registry::get('application/profile/quotaFeed');
                $profile->quota_publication_period = B_Registry::get('application/profile/quotaPublicationPeriod');

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
                    $this->notify($profile->login_email, "register_existing", $profile);
                }
                else
                {
                    $this->notifyRegistration($profile);
                }

                $this->view()->register = true;
                $this->view()->message = $this->translation()->registration_accepted;
            }
            catch(B_Exception $exception)
            {
                $this->view()->message = $this->translation()->registration_failed;

                /* disable unconfirmed profile */

                if(!$profile->register_confirmation)
                {
                    $profile->enabled = false;
                    $profile->save();
                }

                $id = $profile->user_profile_id;
                $_m = "failed to register;\n" . $exception->getMessage();
                $_d = array('method' => __METHOD__, 'user_profile_id' => $id);
                B_Log::write($_m, $exception->getCode(), $_d);
            }
        }
    }

    /**
     * Logout
     *
     * @return  void
     */
    public function A_logout()
    {
        $this->view()->setLayout(null);
        $this->view()->setTemplate(null);
        $this->session()->setActive(false);
        $this->response()->setRedirect(BASE_URL);
    }

    /**
     * Password recovery
     *
     * @return void
     */
    public function A_recovery()
    {
        $this->response()->setXML(true);

        $email = $this->request()->email;
        $profile = UserProfile::getByEmail($email);
        $this->view()->recovery = false;
        $this->view()->message = $this->translation()->recovery_failed;

        /* recovery instructions */

        if(is_object($profile))
        {
            try
            {
                $this->notify($profile->login_email, "recovery", $profile);
                $profile->recovery_message_time = time();
                $profile->recovery_allowed = true;
                $profile->save();
                $this->view()->recovery = true;
                $this->view()->message = $this->translation()->recovery_sent;
            }
            catch(B_Exception $exception)
            {
                $id = $profile->user_profile_id;
                $_m = "failed to recovery;\n" . $exception->getMessage();
                $_d = array('method' => __METHOD__, 'user_profile_id' => $id);
                B_Log::write($_m, $exception->getCode(), $_d);
            }
        }

        /* dummy instructions (not registered profile) */

        else
        {
            try
            {
                $this->notify($email, "dummy");
                $this->view()->recovery = true;
                $this->view()->message = $this->translation()->recovery_sent;
            }
            catch(B_Exception $exception)
            {
                $_m = "failed to send dummy instructions to email (" . $email . ");\n";
                $_m.= $exception->getMessage();
                $_d = array('method' => __METHOD__);
                B_Log::write($_m, $exception->getCode(), $_d);
            }
        }
    }

    /**
     * Confirm register
     *
     * @return  array
     */
    public function A_confirm()
    {
        $this->view()->setLayout('index');

        $email = $this->request()->email;
        $hash = $this->request()->user;
        $this->view()->accepted = false;
        $this->view()->message = 'New profile registration failed.';

        $profile = null;

        if(strpos($email, '@') > 0 && strlen($hash) > 0)
        {
            $profile = UserProfile::getByHash($email, $hash);
        }

        if(is_object($profile))
        {
            if($profile->register_confirmation == true)
            {
                $this->view()->message = 'New profile registration accepted (already done before).';
            }
            else
            {
                $profile->hash = L_Utility::randomString(8);
                $profile->register_confirmation = true;
                $profile->register_confirmation_time = time();
                $profile->save();

                $this->view()->message = 'New profile registration accepted.';
            }

            $this->view()->accepted = true;
        }
    }

    /**
     * Resend register confirmation message
     */
    public function A_resend()
    {
        $this->response()->setXML(true);
        $p = UserProfile::getByPrimaryKey($this->session()->user_profile_id);
        $this->notifyRegistration($p);
    }

    /**
     * Password change action
     * 
     * @return  array
     */
    public function A_password()
    {
        $this->request()->getMethod() == B_Request::METHOD_POST ?
            $this->P_password() :
            $this->G_password();
    }

    /**
     * Password change form
     * 
     * @return  array
     */
    private function G_password()
    {
        $this->view()->setLayout('index');

        $email = $this->request()->email;
        $hash = $this->request()->user;
        $expired = false;
        $profile = null;

        if(strpos($email, '@') > 0 && strlen($hash) > 0)
        {
            $profile = UserProfile::getByHash($email, $hash);
            $expired = is_object($profile) ? $profile->recovery_allowed : true;
        }

        $this->view()->expired = $expired;
        $this->view()->profile = $profile;
    }

    /**
     * Password change save
     * 
     * @return void
     */
    private function P_password()
    {
        $this->response()->setXML(true);

        $email = $this->request()->email;
        $hash = $this->request()->user;
        $current = $this->request()->current;
        $password = $this->request()->password;
        $confirm = $this->request()->passwordc;
        $message = $this->translation()->password_change_failed;

        /* password change (authenticated) */

        if(strlen($current) > 0 && ($id = intval($this->session()->user_profile_id)) > 0)
        {
            $this->authorize();
            $updated = $this->passwordAuthenticated
                ($id, $current, $password, $confirm, $message);
        }
 
        /* password change (not authenticated) */

        if(strpos($email, '@') > 0 && strlen($hash) > 0 && 
           strlen($password) && strlen($confirm))
        {
            $updated = $this->passwordNotAuthenticated
                ($email, $hash, $password, $confirm, $message);
        }

        $this->view()->updated = $updated;
        $this->view()->message = $message;
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
    private function passwordAuthenticated($id, $current, $password, $confirm, &$message)
    {
        $updated = false;

        if(is_object($profile = $profile = UserProfile::getByPrimaryKey($id)))
        {
            if($password != $confirm)
            {
                $message = $this->translation()->password_not_match;
            }
            elseif($profile->login_password_md5 != md5($current))
            {
                $message = $this->translation()->invalid_password;
            }

            /* all ok ! */

            else
            {
                $profile->login_password_md5 = md5($password);
                $profile->recovery_allowed = false;
                $profile->save();

                $updated = true;
                $message = $this->translation()->password_changed;

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
     * @param   string  $hash
     * @param   string  $password
     * @param   string  $confirm
     * @param   string  $message
     * @return  boolean
     */
    private function passwordNotAuthenticated($email, $hash, $password, $confirm, &$message)
    {
        $updated = false;

        if(is_object($profile = $profile = UserProfile::getByHash($email, $hash)))
        {
            if($password != $confirm)
            {
                $message = $this->translation()->password_not_match;
            }
            else
            {
                $profile->hash = L_Utility::randomString(8);
                $profile->login_password_md5 = md5($password);
                $profile->recovery_allowed = false;
                $profile->save();

                $updated = true;
                $message = $this->translation()->password_changed;

                $id = intval($profile->user_profile_id);
                $_d = array ('method' => __METHOD__, 'user_profile_id' => $id);
                self::log("password changed", $_d);
                $this->notify($profile->login_email, "updated", $profile);
                $this->session()->setActive(false);
            }
        }

        return $updated;
    }

    /**
     * Email update action
     * 
     * @return  array
     */
    public function A_email()
    {
        $this->request()->getMethod() == B_Request::METHOD_POST ?
            $this->P_email() :
            $this->G_email();
    }

    /**
     * Email update form
     * 
     * @return  array
     */
    private function G_email()
    {
        $this->view()->setLayout('index');

        $email = $this->request()->email;
        $hash = $this->request()->user;
        $profile = null;
        $new_email = "";

        if(strpos($email, '@') > 0 && strlen($hash) > 0)
        {
            $profile = UserProfile::getByHash($email, $hash);
            $new_email = is_object($profile) ? $profile->update_email_to : "";
        }

        $this->view()->profile = $profile;
        $this->view()->new_email = $new_email;
    }

    /**
     * Email update request/save
     * 
     * @return void
     */
    private function P_email()
    {
        $this->response()->setXML(true);

        $new_email = $this->request()->new_email;
        $email = $this->request()->email;
        $hash = $this->request()->user;
        $password = $this->request()->password;
        $accepted = false;
        $message = $this->translation()->email_change_failed;
        $profile = null;

        /* change request (authenticated) */

        if(strpos($new_email, '@') > 0 && ($id = intval($this->session()->user_profile_id)) > 0)
        {
            $this->authorize();
            $accepted = $this->emailChangeRequest($id, $new_email, $message);
        }
        
        /* change save */

        if(strpos($email, '@') > 0 && strlen($hash) > 0 && strlen($password) > 0)
        {
            $accepted = $this->emailChangeSave($email, $hash, $password, $message);
        }

        // /* disable session */
        // 
        // if($accepted == true)
        // {
        //     $this->session()->setActive(false);
        // }

        $this->view()->accepted = $accepted;
        $this->view()->message = $message;
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

        if(is_object($profile = UserProfile::getByPrimaryKey($id)))
        {
            try
            {
                $profile->update_email_to = $new_email;
                $profile->update_email_message_time = time();
                $profile->save();
 
                $current_email = $profile->login_email_local . '@';
                $current_email.= $profile->login_email_domain;
 
                if($profile->update_email_to != $current_email)
                {
                    $this->notify($new_email, "email_change", $profile);
                    $message = $this->translation()->email_change_accepted;
                    $accepted = true;
                }
                else
                {
                    $message = $this->translation()->email_unchanged;
                }
            }
            catch(B_Exception $exception)
            {
                $_m = "failed to send email change instructions " . 
                      "to email (" . $new_email . ");\n";
                $_m.= $exception->getMessage();
                $_d = array('method' => __METHOD__);
                B_Log::write($_m, $exception->getCode(), $_d);
            }
        }

        return $accepted;
    }

    /**
     * Email change save
     * 
     * @param   string  $email      New user profile login email
     * @param   string  $hash       User Profile Hash
     * @param   string  $password   User Profile password
     * @param   string  $message
     * @return  boolean
     */
    private function emailChangeSave($email, $hash, $password, &$message)
    {
        $accepted = false;

        if(is_object($profile = UserProfile::getByHash($email, $hash)))
        {
            if($profile->login_password_md5 != md5($password))
            {
                $message = $this->translation()->password_not_match;
            }
            else
            {
                if(strlen(($new_email = $profile->update_email_to)) > 0)
                {
                    $profile->login_email = $new_email;
                    $profile->hash = L_Utility::randomString(8);
                    $profile->save();

                    $accepted = true;
                    $message = $this->translation()->email_changed;

                    $id = $profile->user_profile_id;
                    $_d = array ('method' => __METHOD__, 'user_profile_id' => $id);
                    self::log("email changed", $_d);
                    $this->notify($new_email, "updated", $profile);
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
    public function A_edit()
    {
        $this->authorize();

        $this->request()->getMethod() == B_Request::METHOD_POST ?
            $this->P_edit() :
            $this->G_edit();
    }

    /**
     * Profile editing form
     *
     * @return array|null
     */
    private function G_edit()
    {
        $this->view()->setLayout('dashboard');
        $id = intval($this->session()->user_profile_id);

        /* blog list */

        $blogs = UserBlog::findByUser($id, $enabled=true);
        $this->view()->blogs = $blogs;
        $this->view()->settings = UserDashboard::getByUser($id);
        
        /* profile */

        $this->view()->profile = UserProfile::getByPrimaryKey($id);

        if(is_object($this->view()->profile) == false) 
        {
            $this->response()->setRedirect(BASE_URL);
        }

        $culture = $this->session()->getCulture();

        /* territory */

        $territory = array();

        // try catch to avoid unknown locales
        try {                 $tl = Zend_Locale::getTranslationList('Territory', $culture); }
        catch(Exception $e) { $tl = Zend_Locale::getTranslationList('Territory', 'en_US'); }      

        foreach($tl as $k => $v)
        {
            if(strlen($k)==2 && $k!='ZZ')
            {
                $territory[$k] = $v; 
            }
        }

        asort($territory);

        $this->view()->territory = $territory;

        /* culture */

        $dl = L_Utility::getDefaultCulture();

        $language = array();

        // try catch to avoid unknown locales
        try {                 $tl = Zend_Locale::getTranslationList('Language', $culture); }
        catch(Exception $e) { $tl = Zend_Locale::getTranslationList('Language', 'en_US'); }      

        foreach($tl as $k => $v)
        {
            if(in_array($k, $dl))
            {
                $language[$k] = ucwords($v);
            }
        }

        $this->view()->language = $language;

        /* quota */

        $this->view()->blog_total = UserBlog::total($id);
        $this->view()->feed_total = UserBlogFeed::total($id);
        $this->view()->publication_period_total = 0; // TODO
        $this->view()->publication_period = B_Registry::get('application/queue/publicationPeriod'); // TODO human readable
    }

    /**
     * Profile editing save
     *
     * @return void
     */
    private function P_edit()
    {
        $this->response()->setXML(true);

        $id = intval($this->session()->user_profile_id);
        $profile = ($id > 0) ? UserProfile::getByPrimaryKey($id) : null;
        $this->view()->saved = false;
        $this->view()->message = $this->translation()->edit_failed;

        if(!is_object($profile))
        {
            $_m = "invalid user profile";
            $_d = array('method' => __METHOD__, 'user_profile_id' => $id);
            throw new B_Exception($_m, E_WARNING, $_d);
        }

        foreach(UserProfile::$allow_write as $k)
        {
            if(array_key_exists($k, $_REQUEST))
            {
                $profile->{$k} = $this->request()->{$k};
            }
        }

        try
        {
            $profile->save();
            // $this->session()->setCulture($profile->local_culture); // not implemented
            $this->session()->setTimezone($profile->local_timezone);
            $this->view()->saved = true;
            $this->view()->message = $this->translation()->edit_saved;
        }
        catch(B_Exception $exception)
        {
            $_m = "failed to save profile after editing";
            $_d = array('method' => __METHOD__, 'user_profile_id' => $id);
            B_Exception::forward($_m, E_WARNING, $exception, $_d);
        }
    }

    /**
     * Get/Set user preferences
     *
     * @param   string  $name
     */
    public function A_preference()
    {
        $this->authorize();

        $this->response()->setXML(true);

        $pk = $this->request()->k;
        $pv = $this->request()->v;
        $id = $this->session()->user_profile_id;

        if($pk && $pv)
        {
            UserProfile::setPreference($id, array($pk => $pv));
            $this->view()->k = $pk;
            $this->view()->v = $pv;
            $cs = ((array) $this->session()->profile_preference);
        }
        elseif($pk)
        {
            $pr = UserProfile::getPreference($id);
            $this->view()->k = $pk;
            $pv = $pr[$pk];
            $this->view()->v = $pv;
            $cs = ((array) $this->session()->profile_preference);
        }
        else
        {
            $pr = UserProfile::getPreference($id);
            $this->view()->preference = $pr;
        }
    }

    /**
     * Get timezone options
     */
    public function A_timezone()
    {
        // $this->authorize();

        $this->response()->setXML(true);

        $territory = $this->request()->territory;
        $culture = $this->session()->getCulture();
        $timezone = array();

        foreach(Zend_Locale::getTranslationList('TerritoryToTimezone', $culture) as $k => $v)
        {
            if(strlen($territory)>0 ? ($territory==$v) : true)
            {
                $timezone[] = $k;
            }
        }

        if(count($timezone)==0) $timezone = array("UTC");

        $this->view()->timezone = $timezone;
    }

    /**
     * Send notification mail
     *
     * @param   string      $email
     * @param   string      $template
     * @param   UserProfile $profile
     * @return  boolean
     */
    private function notify($email, $template, $profile=null)
    {
        $mailer = new L_Mailer();

        $subject = "mail_" . $template . "_subject";
        $mailer->setSubject($this->translation()->{$subject});
        $template = ucfirst($this->request()->getController()) . "/mail_" . $template;

        ob_start();
        include APPLICATION_PATH . "/view/template/" . $template . ".php";
        $mailer->isHTML(true);
        $mailer->setBody(ob_get_clean());

        return $mailer->send($email, $template);
    }

    private function notifyRegistration($profile)
    {
        $this->notify($profile->login_email, "register_new", $profile);
        $profile->register_message_time = time();
        $profile->save();
    }

    /**
     * Check reCaptcha
     *
     * @param   string  $c  recaptcha chalenge field
     * @param   string  $r  recaptcha response field
     * @return  boolean
     */
    private static function checkRecaptcha($c, $r)
    {
        require_once 'recaptcha/recaptchalib.php';

        if(strlen(($pk = B_Registry::get('recaptcha/privateKey')))==0)
            throw new B_Exception('recaptcha private key is not set', E_ERROR);

        $res = recaptcha_check_answer($pk, $_SERVER['REMOTE_ADDR'], $c, $r);

        return $res->is_valid;
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

        B_Log::write($_m, E_NOTICE, $_d);
    }
}
