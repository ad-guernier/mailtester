<?php
namespace Guern\Classes;

class SendWithSwiftMailer {
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
        $transport = (new \Swift_SmtpTransport())
            ->setHost($this->smtp_host)
            ->setPort($this->smtp_port)
            ->setEncryption($this->smtp_encryption)
            ->setUsername($this->smtp_identifier)
            ->setPassword($this->smtp_pwd)
            ->setStreamOptions(['ssl' => ['allow_self_signed' => true, 'verify_peer' => false]]) 
        ;
        $mailer = new \Swift_Mailer($transport);

        $logger = new \Swift_Plugins_Loggers_ArrayLogger();
        $mailer->registerPlugin(new \Swift_Plugins_LoggerPlugin($logger));

        $message = new \Swift_Message();
        $message->setTo($this->mail_to);
        $message->setFrom($this->mail_from);
        $message->setSubject('Test SwiftMailer');
        $message->setBody(
            'Host : ' . $mailer->getTransport()->getHost() . "\n"
            . 'Port : ' . $mailer->getTransport()->getPort() . "\n"
            . 'Encryption : ' . $mailer->getTransport()->getEncryption() . "\n"
            . 'Username : ' . $mailer->getTransport()->getUsername() . "\n"
            . 'StreamOptions : ' . json_encode($mailer->getTransport()->getStreamOptions())
        );


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