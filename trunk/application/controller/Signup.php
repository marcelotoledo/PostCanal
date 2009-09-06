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
    public function before()
    {
        $this->view()->setLayout('index');
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
}
