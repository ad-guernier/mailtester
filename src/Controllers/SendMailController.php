<?php
namespace Guern\Controllers;

use Guern\Controllers\FrontController;
use Guern\Classes\SendWithSwiftMailer;

class SendMailController extends FrontController {
    private $lib;
    private $param;

    public function __construct() {
        $this->lib = $_POST['lib'];
        $this->param = $_POST;
        parent::__construct();
    }

    private function sendMail() {
        switch ($this->lib) {
            case 'SwiftMailer':
                $mailer = new sendWithSwiftMailer(
                    $this->param['smtp_identifier'],
                    $this->param['smtp_pwd'],
                    $this->param['smtp_host'],
                    $this->param['smtp_encryption'],
                    $this->param['smtp_port'],
                    $this->param['mail_from'],
                    $this->param['mail_to'],
                    $this->param['try']
                );
                $response = $mailer->send();
                
                break;
            
            default:
                # code...
                break;
        }

        return $response;
    }

    public function view() {
        $response = $this->sendMail();
        echo $this->twig->render('mailResponse.html.twig', [
            'response' => $response
        ]);
    }
}