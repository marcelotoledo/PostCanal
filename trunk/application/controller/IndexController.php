<?php

/* AUTOBLOG INDEX CONTROLLER */

class IndexController extends AB_Controller
{
    public function indexAction()
    {
        $user_profile = new UserProfile();

        $profile = current(UserProfile::find(array('user_profile_id' => 1)));

        return array
        (
            'login_email' => $profile->login_email,
            'created_at' => $profile->created_at
        );
    }
}
