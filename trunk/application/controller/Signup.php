<?php

/**
 * Signup controller class
 * 
 * @category    PostCanal
 * @package     Application Controller
 * @author      Rafael Castilho <rafael@castilho.biz>
 */

class C_Signup extends B_Controller
{
    /* configure controller */

    public function configure($action_name)
    {
        $this->hasSession(false);
    }

    public function before()
    {
        $this->view()->setLayout('index');

        if(B_Registry::get('application/maintenance')=='true')
        {
            $this->response()->setRedirect(B_Request::url('maintenance'));
        }
    }

    /**
     * Default action
     */
    public function A_index()
    {
        /* territory (country) */

        $territory = array();

        $tl = array();
        
        // try catch to avoid unknown locales
        try {                 $tl = Zend_Locale::getTranslationList('Territory'); }
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
    }

    /**
     * Welcome message
     */
    public function A_welcome()
    {
        /* void */
    }

    /**
     * Invitation action
     */
    public function A_invitation()
    {
        $this->request()->getMethod() == B_Request::METHOD_POST ?
            $this->P_invitation() :
            $this->G_invitation();
    }

    /**
     * Invitation message
     */
    public function G_invitation()
    {
        $this->view()->setLayout('default');
    }

    /**
     * Invitation post
     */
    public function P_invitation()
    {
        $this->response()->setXML(true);

        if(!is_object(ProfileInvitation::getByEmail($this->request()->email)))
        {
            $i = new ProfileInvitation();
            $i->name = $this->request()->name;
            $i->invitation_email = $this->request()->email;
            $i->save();
        }
    }
}
