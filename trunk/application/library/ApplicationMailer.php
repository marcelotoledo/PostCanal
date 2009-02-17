<?php

/**
 * Application mailer class
 * 
 * @category    Blotomate
 * @package     Application library
 * @author      Rafael Castilho <rafael@castilho.biz>
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
    public function __construct($mailer='default', $sender='default')
    {
        $registry = AB_Registry::singleton();
        $mailer_config = $registry->mailer->{$mailer};
        $sender_config = $mailer_config->sender->{$sender};

        $server = $mailer_config->server;

        $config = array
        (
            'auth'     => $mailer_config->auth,
            'ssl'      => $mailer_config->ssl,
            'port'     => $mailer_config->port,
            'username' => $sender_config->username,
            'password' => $sender_config->password
        );

        $this->from = $sender_config->email;
        $this->transport = new Zend_Mail_Transport_Smtp($server, $config);
        $this->charset = "UTF-8";
        $this->relay_time = $mailer_config->relay->time;
        $this->relay_count = $mailer_config->relay->count;
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
     * @param   string          $recipient      Email address
     * @param   string          $identifier     Message identifier
     * @throws  AB_Exception
     * @return  boolean
     */
    public function send($recipient, $identifier=null)
    {
        $mail = new Zend_Mail($this->charset);
        $mail->setFrom($this->from);
        $sent = false;

        if(self::allowRelay($recipient, 
                            $identifier,
                            $this->relay_time, 
                            $this->relay_count))
        {
            $mail->addTo($recipient);
            $mail->setSubject($this->subject);
            $mail->setBodyHtml($this->body);

            try
            {
                $mail->send($this->transport);
                self::setRelay($recipient, $identifier);
                $sent = true;
            }
            catch(Exception $exception)
            {
                $message = "sending mail to recipient (" . $recipient . ") failed";
                $data = array('method' => __METHOD__);
                AB_Exception::forward($message, E_USER_NOTICE, $exception, $data);
            }
        }

        return $sent;
    }

    /**
     * Set relay
     *
     * @param   string      $recipient      Email address
     * @param   string      $identifier     Message identifier
     * @return  boolean
     */
    private static function setRelay($recipient, $identifier)
    {
        $relay = new ApplicationMailerRelay();
        $relay->recipient = $recipient;
        $relay->identifier_md5 = md5($identifier);
        $relay->save();
    }

    /**
     * Relay
     * 
     * Check if there is repeated sending of messages and
     * return true/false when allowed to send email
     *
     * @param   string  $recipient  Email address
     * @param   string  $identifier Message identifier
     * @param   integer $delay      Time in seconds
     * @return  boolean
     */
    private static function allowRelay($recipient, 
                                       $identifier, 
                                       $time=3600,
                                       $count=2)
    {
        $data = array();

        $sql = "SELECT COUNT(*) AS total ";
        $sql.= "FROM application_mailer_relay ";

        $sql.= "WHERE recipient = ? ";
        $data[] = $recipient;

        $sql.= "AND identifier_md5 = ? ";
        $data[] = md5($identifier);

        $sql.= "AND created_at > ? ";
        $time = mktime(date('H'), date('i'), ($time * -1));
        $data[] = date("Y-m-d H:i:s", $time);

        $deny = false;

        if(is_object($result = AB_Model::selectRow($sql, $data)))
        {
            $deny = $result->total >= $count;
        }

        if($deny)
        {
            $message = "mailer relay denied to recipient (" . $recipient . ") " .
                       "and identifier (" . $identifier . ")";
            $data = array('method' => __METHOD__);
            throw new AB_Exception($message, E_USER_WARNING, $data);
        }

        return $deny ^ true;
    }
}
