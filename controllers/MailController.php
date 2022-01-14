<?php
namespace controllers;

use PHPMailer\PHPMailer\PHPMailer;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class MailController extends Controller {
    public $mail;
    protected $username;
    protected $password;
    public function __construct() {
        parent::__construct();
        $this->mail = new PHPMailer();
        $this->username = $this->config['mail']['username'];
        $this->password = $this->config['mail']['password'];
    }
    public function send($to, $subject, $body, $link = '') {
        //Define SMTP connection
        $this->mail->isSMTP();
        //Set host
        $this->mail->Host = 'smtp.gmail.com';
        $this->mail->SMTPAuth = 'true';
        //Set port
        $this->mail->Port = '587';
        //Security
        $this->mail->SMTPSecure = 'tls';
        //Authentication
        $this->mail->Username = $this->username;
        $this->mail->Password = $this->password;
        //Subject
        $this->mail->Subject = $subject;
        //Enable HTML
        $this->mail->isSMTP();
        //Body
        $this->mail->Body = $body;
        $this->mail->addAddress( $to );
        //Append link if there is any
        if ( !empty($link)) {
            $this->mail->Body .= '<a href="'.$link.'">'.$link.'</a>';
        }
        //Send the letter
        if ( $this->mail->Send() ) {
            return true;
        } else {
            return false;
        }
    }
}