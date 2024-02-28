<?php
    (!defined('ROOT')) ? define('ROOT', dirname(__FILE__, 2)) : "";
    require_once ROOT .'/core/routing/routing.php';

    $models = [
        "mail" => "mail"
    ];

    Routing::model(null, $models);

    function sendActivationMail($email, $alias, $token, $code){
        $mail = new Mail();
        $website = Routing::config('project')['website'];
        $link =  $website ."/activation?token=" .$token ."&code=" .$code;
        $templatePath = ROOT . '/storage/templates/mail/activation.php';

        if (file_exists($templatePath)) {
            $body = file_get_contents($templatePath);
            $body = str_replace(['<?php', '?>'], '', $body); // Eliminar las etiquetas PHP si existen
            // Reemplazar placeholders con valores correspondientes
            $body = str_replace('{{name}}', $alias, $body);
            $body = str_replace('{{link}}', $link, $body);
            $body = str_replace('{{code}}', $code, $body);
        } else {
            $body = 'Hola ' .$alias ." usa el código: " .$code ." en el enlace: " .$link;
        }

        $mail->send("admin", [$email], "Activa tu cuenta con: " .$code, true, $body);
    }