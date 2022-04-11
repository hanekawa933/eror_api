<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class PHPmailer_lib
{
    public function __construct()
    {
        log_message('Debug', 'PHPMailer class is loaded.');
    }

    public function load()
    {
        // Include PHPMailer library files
        require_once APPPATH . 'third_party/PHPMAILER/Exception.php';
        require_once APPPATH . 'third_party/PHPMAILER/PHPMailer.php';
        require_once APPPATH . 'third_party/PHPMAILER/SMTP.php';

        $mail = new PHPMailer;
        return $mail;
    }
}
