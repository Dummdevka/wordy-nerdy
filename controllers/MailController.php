<?php

use PHPMailer\PHPMailer\PHPMailer;

class MailController extends Controller {
    public $mail;
    public function __construct()
    {
        parent::__construct();
        $this->mail = new PHPMailer();
    }
}