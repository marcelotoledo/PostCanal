<?php

/**
 * Support controller class
 * 
 * @category    PostCanal
 * @package     Application Controller
 * @author      Rafael Castilho <rafael@castilho.biz>
 */

class C_Support extends B_Controller
{
    const EMAIL_ADDRESS_MAX_SIZE =   256;
    const EMAIL_SUBJECT_MAX_SIZE =  1000; //  1kB  
    const EMAIL_MESSAGE_MAX_SIZE = 80000; // 80kB


    public function before()
    {
        $this->view()->setLayout('index');
    }

    /**
     * Default action
     */
    public function A_index()
    {
        if($this->request()->getMethod() == B_Request::METHOD_POST)
        {
            $this->response()->setXML(true);

            $name    = $this->request()->name;
            $email   = $this->request()->email;
            $subject = $this->request()->subject;
            $message = $this->request()->message;

            /* check for overflow */

            if(strlen($email)  >self::EMAIL_ADDRESS_MAX_SIZE ||
               strlen($subject)>self::EMAIL_SUBJECT_MAX_SIZE ||
               strlen($message)>self::EMAIL_MESSAGE_MAX_SIZE)
            {
                B_Log::write(sprintf('sending support email denied for address (%s) because it has excess bytes', substr($email, 0, self::EMAIL_ADDRESS_MAX_SIZE)), E_WARNING, array('method' => __METHOD__));
                $this->view()->sent = false;
                return false;
            }

            /* check form values */

            if(strlen($name)        ==  0 ||
               strpos($email, '@') === -1 ||
               strlen($subject)     ==  0 ||
               strlen($message)     ==  0)
            {
                $this->view()->sent = false;
                return false;
            }
                
            /* determine identifier (ip address) for mailer relay */

            $identifier = '';

            if(array_key_exists('REMOTE_ADDR', $_SERVER) && 
               strlen($_SERVER['REMOTE_ADDR'])>0)
            {
                $identifier = $_SERVER['REMOTE_ADDR'];
            }

            if(strlen($identifier)==0)
            {
                B_Log::write(sprintf('sending support email denied for address (%s) because it was not possible to determine an identifier', $email), E_WARNING, array('method' => __METHOD__));
                $this->view()->sent = false;
                return false;
            }

            /* get support recipient from registry */

            $to = ((string) B_Registry::get('application/support/email'));
            $sp = ((string) B_Registry::get('application/support/subjectPrefix'));

            if(strpos($to, '@')===-1)
            {
                B_Log::write('sending support email failed because it was not possible to determine an recipient from registry', E_ERROR, array('method' => __METHOD__));
                $this->view()->sent = false;
                return false;
            }

            /* send email */

            try
            {
                $mailer = new L_Mailer();
                $mailer->setSubject($sp . ' ' . $subject);
                $body = sprintf("mail from: %s <%s>", $name, $email) . "\n\n" . $message;
                $mailer->isHTML(false);
                $mailer->setBody($body);
                $mailer->send($to, sprintf('SUPPORT REQUEST FROM %s', $identifier));
                $this->view()->sent = true;
            }
            catch(Exception $e)
            {
                B_Log::write("sending support email failed;\n" . $e->getMessage(), E_ERROR, array('method' => __METHOD__));
                $this->view()->sent = false;
            }
        }
    }
}
