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
        $this->mail->isHTML(true);
        //Body
        $this->mail->Body = $body;
        $this->mail->AltBody = $body;
        
        $this->mail->addAddress( $to );
        //Append link if there is any
        if ( !empty($link)) {
            $this->mail->Body .= '<br>';
            $this->mail->Body .= '<a href="'.$link.'"><button type="button" style="height:30px;width:70px;background-color:#60d394;border:none;border-radius:3px;">Confirm!</button></a>';
        }
        //Send the letter
        if ( $this->mail->Send() ) {
            return true;
        } else {
            return false;
        }
    }
}