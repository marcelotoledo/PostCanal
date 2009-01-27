<?php

/**
 * Default helper class
 *
 * @category    Blotomate
 * @package     View
 */
class DefaultHelper extends AB_Helper
{
    public function sessionEmail()
    {
        $session = SessionController::recoverSession();
        $email = null;

        if(is_object($session))
        {
            $email = $session->user_profile_login_email;
        }

        echo $email;
    }
}
