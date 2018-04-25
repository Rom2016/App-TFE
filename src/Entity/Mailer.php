<?php
/**
 * Created by PhpStorm.
 * User: Romain
 * Date: 20/04/2018
 * Time: 12:22
 */

namespace App\Entity;
use Symfony\Bundle\SwiftmailerBundle;

class Mailer
{
    private $source = 'noReply@tfe.be';
    private $destination;
    private $topic;
    private $body;

    /**
     * Mailer constructor.
     * @param $destination
     * @param $topic
     * @param $body
     */
    public function __construct($destination, $topic, $body)
    {
        $this->destination = $destination;
        $this->topic = $topic;
        $this->body = $body;
    }


    public function sendMailNewUSer()
    {
        $transport = (new Swift_SmtpTransport('smtp.gmail.com', 25))
            ->setUsername('app.tfe2018')
            ->setPassword('6kJcZ6b3')
        ;
        $mailer = new \Swift_Mailer($transport);
        $message = (new \Swift_Message($this->topic))
            ->setFrom($this->source)
            ->setTo($this->destination)
            ->setBody(
               $this->body,
                'text/html'
            );
        $mailer->send($message);
    }

}