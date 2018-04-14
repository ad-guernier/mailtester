<?php
namespace Guern\Classes;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class SendWithPhpMailer {
    private $smtp_identifier;
    private $smtp_pwd;
    private $smtp_host;
    private $smtp_encryption;
    private $smtp_port;
    private $mail_from;
    private $mail_to;
    private $try;

    public function __construct($smtp_identifier, $smtp_pwd, $smtp_host, $smtp_encryption, $smtp_port, $mail_from, $mail_to, $try){
        $this->smtp_identifier = $smtp_identifier;
        $this->smtp_pwd = $smtp_pwd;
        $this->smtp_host = $smtp_host;
        $this->smtp_encryption = $smtp_encryption;
        $this->smtp_port = $smtp_port;
        $this->mail_from = $mail_from;
        $this->mail_to = $mail_to;
        $this->try = $try;
    }

    public function send () {
        try {
            //Server settings
            $mail->SMTPDebug = 2;                                 // Enable verbose debug output
            $mail->isSMTP();                                      // Set mailer to use SMTP
            $mail->Host = $this->smtp_identifier;  // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;                               // Enable SMTP authentication
            $mail->Username = $this->smtp_identifier;                 // SMTP username
            $mail->Password = $this->smtp_pwd;                           // SMTP password
            $mail->SMTPSecure = $this->smtp_encryption;                            // Enable TLS encryption, `ssl` also accepted
            $mail->Port = $this->smtp_port;                                    // TCP port to connect to
        
            //Recipients
            $mail->setFrom($this->mail_from, 'Mailer');
            $mail->addAddress($this->mail_to);     // Add a recipient
        
            //Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = 'Here is the subject';
            $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
            $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
        
            $mail->send();

        } catch (Exception $e) {
            echo 'Message could not be sent.';
            echo 'Mailer Error: ' . $mail->ErrorInfo;
        }


        $attempts = 0;
        $errorStack = [];

        do {
            try {
                $result = $mailer->send($message);
                $errorStack[$attempts] = [
                    'swiftLoggerSuccess' => $logger->dump(),
                ];
            } catch (Exception $e) {
                $errorStack[$attempts] = [
                    'exception' => $e,
                    'exceptionMessage' => $e->getMessage(),
                    'swiftLoggerError' => $logger->dump(),
                    'lastError' => print_r(error_get_last(), true)
                ];
                $attempts++;
                $result = 0;
                sleep(1);
                continue;
            }
            break;
        } while ($attempts < $this->try);

        return [
            'maxTry' => $this->try,
            'error' => $errorStack,
            'attempts' => $attempts,
            'result' => (bool)$result
        ];
    }   
}