<?php

/**
 * Blog controller class
 * 
 * @category    PostCanal
 * @package     Application Controller
 */

class C_Blog extends B_Controller
{
    /**
     * Default action
     *
     * @return void
     */
    public function A_index()
    {
        header('Location: http://blog.postcanal.com/');
        exit(0);
    }
}
