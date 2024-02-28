<?php
    
    (!defined('ROOT')) ? define('ROOT', dirname(__FILE__, 3)) : "";
    require_once ROOT . '/vendor/autoload.php';
    require_once ROOT . '/core/routing/routing.php';

    $models = [
        'lib' => 'exception'
    ];

    Routing::model(null, $models, 'r_o');

    interface mailInterface {
        public function send($account, $toMails, $subject, $isHtml, $body, $attachments = []);
    }

    class Mail implements mailInterface {
        
        private $config;
        public $mail;

        public function __construct() {
            $this->config = Routing::config("mail", null);
            $this->mail = new PHPMailer\PHPMailer\PHPMailer(true); 
        }

        public function send($account, $toMails, $subject, $isHtml, $body, $attachments = []) {
            try {
                if (!is_array($this->config) || !isset($this->config["senders"][$account])) {
                    return Responser::exception("Sender doesn't exist", 400, null, null);
                }

                $this->mail->isSMTP();
                $this->mail->Host = $this->config["server"]["host"];
                $this->mail->Port = $this->config["server"]["port"];
                $this->mail->SMTPSecure = $this->config["server"]["crypt"];
                $this->mail->SMTPAuth = true;
                $this->mail->CharSet = 'UTF-8';

                $this->mail->Username = $this->config["senders"][$account]["email"];
                $this->mail->Password = $this->config["senders"][$account]["password"];

                $this->mail->setFrom($this->config["senders"][$account]["email"], $this->config["senders"][$account]["name"]);

                // Add multiple clients
                foreach ($toMails as $toMail) {
                    $this->mail->addAddress($toMail);
                }

                $this->mail->Subject = $subject;

                $this->mail->isHTML($isHtml);
                $this->mail->Body = $body;

                if(is_array($attachments)){
                    foreach ($attachments as $attachment) {
                        if(is_array($attachment)){
                            $this->mail->addAttachment($attachment['path'], $attachment['name']);
                        }
                    }
                }

                $this->mail->send();
                return Responser::toSystem(200, "Mail sent successfully", NULL);
            } 
            catch (Exception $e) {
                return Responser::exception("Error sending mail " .(string)$e, 500, null, ["exception" => (string)$e]);
            }
        }
    }
