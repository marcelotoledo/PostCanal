<?php

/**
 * Application mailer class
 * 
 * @category    Autoblog
 * @package     Library
 */
class ApplicationMailer
{
    /**
     * From
     * @var string
     */
    private $from;

    /**
     * Subject
     * @var string
     */
    private $subject;

    /**
     * Body
     * @var body
     */
    private $body;

    /**
     * Mail transport
     * @var Zend_Mail_Transport
     */
    private $transport;

    /**
     * Charset
     * @var string
     */
    private $charset;

    /**
     * Relay time
     * @var integer
     */
    private $relay_time;

    /**
     * Relay count
     * @var int
     */
    private $relay_count;


    /**
     * Application mailer constructor
     *
     * @return  void
     */
    public function __construct()
    {
        $registry = AB_Registry::singleton();

        $server = $registry->mailer->server;

        $config = array
        (
            'auth'     => $registry->mailer->auth,
            'ssl'      => $registry->mailer->ssl,
            'port'     => $registry->mailer->port,
            'username' => $registry->mailer->sender->username,
            'password' => $registry->mailer->sender->password
        );

        $this->from = $registry->mailer->sender->email;
        $this->transport = new Zend_Mail_Transport_Smtp($server, $config);
        $this->charset = "UTF-8";
        $this->relay_time = $registry->mailer->relay->time;
        $this->relay_count = $registry->mailer->relay->count;
    }

    /**
     * Set subject
     *
     * @param   string  $subject
     * @return  void
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * Set body
     *
     * @param   string  $body
     * @return  void
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * Send email
     *
     * @param   string      $recipient  Email address
     * @param   string      $type       Message type
     * @throws  Exception
     * @return  boolean
     */
    public function send($recipient, $type=null)
    {
        $mail = new Zend_Mail($this->charset);
        $mail->setFrom($this->from);
        $sent = false;

        if(self::allowRelay($recipient, 
                            $type,
                            $this->relay_time, 
                            $this->relay_count))
        {
            $mail->addTo($recipient);
            $mail->setSubject($this->subject);
            $mail->setBodyHtml($this->body);

            try
            {
                $mail->send($this->transport);
                self::setRelay($recipient, $type);
                $sent = true;
            }
            catch(Exception $exception)
            {
                $message = "sending mail failed; ";
                $message.= $exception->getMessage();
                throw new Exception($message);
            }
        }

        return $sent;
    }

    /**
     * Set relay
     *
     * @param   string  $recipient
     * @return  boolean
     */
    private static function setRelay($recipient, $type=null)
    {
        $session_id = session_id();
        $remote_ip_address = $_SERVER['REMOTE_ADDR'];
        
        $relay = new ApplicationMailerRelay();

        if(!empty($session_id))
        {
            $relay->session_id = $session_id;
        }

        if(!empty($remote_ip_address))
        {
            $relay->remote_ip_address = $remote_ip_address;
        }

        if(!empty($type))
        {
            $relay->message_type = $type;
        }

        $relay->recipient = $recipient;
        $relay->save();
    }

    /**
     * Relay
     * 
     * Check if there is repeated sending of messages and
     * return true/false when allowed to send email
     *
     * @param   string  $recipient  Email address
     * @param   string  $type       Message type
     * @param   integer $delay      Time in seconds
     * @return  boolean
     */
    private static function allowRelay($recipient, 
                                       $type, 
                                       $time=3600,
                                       $count=2)
    {
        $session_id = session_id();
        $remote_ip_address = $_SERVER['REMOTE_ADDR'];
        $data = array();

        $sql = "SELECT COUNT(*) AS total ";
        $sql.= "FROM application_mailer_relay ";
        $sql.= "WHERE created_at > ? AND (recipient = ? ";

        $data[] = date("Y-m-d H:i:s", 
                       mktime(date('H'), date('i'), ($time * -1)));
        $data[] = $recipient;

        $sql.= "OR (session_id = ? AND remote_ip_address = ?)) ";
        $data[] = $session_id;
        $data[] = $remote_ip_address;

        $sql.= "AND message_type = ?";
        $data[] = $type;

        $deny = false;

        if(is_object($result = AB_Model::selectRow($sql, $data)))
        {
            $deny = $result->total >= $count;
        }

        if($deny)
        {
            $message = "mailer relay denied ";

            if(!empty($type))
            {
                $message.= "message type (" . $type . "), "; 
            }
            if(!empty($session_id))
            {
                $message.= "session id (" . $session_id. "), ";
            }
            if(!empty($remote_ip_address))
            {
                $message.= "remote ip address (" . $remote_ip_address . "), ";
            }

            $message.= " recipient (" . $recipient . ")";

            AB_Log::write($message, AB_Log::PRIORITY_WARNING);
        }

        return $deny ^ true;
    }
}
