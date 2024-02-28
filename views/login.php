<?php
    $sid = 1;

    (!defined('ROOT')) ? define('ROOT', dirname(__FILE__, 2)) : "";
    require_once ROOT .'/views/core.php';

    $models = ["lib" => "webBuilder"];
    Routing::model(null, $models);
?>

    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login</title>
        <?php
            $wb = new WebBuilder();

            echo $wb->getScript('config.js');
            echo $wb->getScript('login.js');
        ?>
    </head>
    <body>
        <main>
            <div>
                <form id="app-login-form">
                    <input type="text" name="username" id="login-username" placeholder="Username">
                    <input type="password" name="password" id="login-password" placeholder="Password">
                    <input type="checkbox" name="persist" id="login-persist">
                    <label for="login-persist">Keep connected</label>
                    <input type="submit" value="Login">
                </form>
            </div>
        </main>
    </body>
    </html>