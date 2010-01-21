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
     * Configure controller
     */
    public function configure($action_name)
    {
        $this->hasView(false);
        $this->hasSession(false);
        $this->hasTranslation(false);
    }

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
