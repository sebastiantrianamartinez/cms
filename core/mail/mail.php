<?php
    (!defined('ROOT')) ? define("ROOT", dirname(__FILE__, 3)) : "";
    require_once ROOT . '/core/routing/routing.php';
    require_once ROOT . '/vendor/autoload.php';

    $modules = [
        "lib" => "responser",
        "excp" => "throwing"
    ];

    routing::bigRouting($modules);

    class Mail {
        private $config;
        public $mail;

        public function __construct() {
            $this->config = routing::config("mail")["data"];
            $this->mail = new PHPMailer\PHPMailer\PHPMailer(true); 
        }

        public function send($sender, $toMail, $toName, $subject, $isHtml, $body) {
            try {
                if(!isset($this->config["senders"][$account])){
                    //sender validation 
                    return responser::systemResponse(400, "Sender doesn't exists", NULL);
                }
                //SMTP server config
                $this->mail->isSMTP();
                $this->mail->Host = $this->config["server"]["host"];
                $this->mail->Port = $this->config["server"]["port"];
                $this->mail->SMTPSecure = $this->config["server"]["crypt"];
                $this->mail->SMTPAuth = true;
                $this->mail->CharSet = 'UTF-8';
                
                $this->mail->Username = $this->config["senders"][$account]["email"];
                $this->mail->Password = $this->config["senders"][$account]["password"];

                $this->mail->setFrom($this->config["senders"][$account]["email"], $this->config["senders"][$account]["name"]);
                $this->mail->addAddress($toMail, $toName);
                $this->mail->Subject = $subject;

                $this->mail->isHTML($isHtml);
                $this->mail->Body = $body;

                $this->mail->send();
                return responser::systemResponse(200, "mail sent successfully", NULL);
            } 
            catch (Exception $e) {
                throwing::sendLog('mail', 2, $e);
                return responser::systemResponse(500, "error sending mail", ["exception" => (string)$e]);
            }
        }
    }
?>
