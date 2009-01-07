<?php

/* AUTOBLOG INDEX CONTROLLER */

class IndexController extends AB_Controller
{
    public function indexAction()
    {
        $user_profile = new UserProfile();

        $first = UserProfile::selectModelWhere(array('id' => 1));

        print_r($first);

        return array
        (
            'hello' => 'world',
            'my' => 'test'
        );
    }
}
