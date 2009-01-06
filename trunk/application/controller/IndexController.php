<?php

/* AUTOBLOG INDEX CONTROLLER */

class IndexController extends AB_Controller
{
    public function indexAction()
    {
        return array
        (
            'hello' => 'world',
            'my' => 'test'
        );
    }
}
