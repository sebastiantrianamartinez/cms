<?php
    (!defined('ROOT')) ? define('ROOT', dirname(__FILE__, 2)) : "";
    require_once ROOT .'/core/routing/routing.php';

    $service = [
        "id" => 1
    ];
    require_once ROOT .'/auth/headers/interface_header.php';
    
    if(is_int($user["id"]) && $user["id"] > 0){
        $website = routing::config('project', 'dns')["data"]["website"];
        header('location: ' .$website);
        die();
    }

    $modules = [
        "lib" => "htmlFormatter",
    ];
    routing::bigRouting($modules);
    session_start();
?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login</title>
        <?php
            htmlFormatter::printFaviconLink('favicon.ico', true);

            htmlFormatter::printStylesheetLink('normalize.css', true);
            htmlFormatter::printStylesheetLink('login.css', true);

            htmlFormatter::printScriptLink('config.js', true, true);
        ?>
    </head>
    <body>
        <main>
            <div class="login-container">
                <div class="login-info" id="form-info-panel">
                    <p id="form-info-panel-msg">ⓘ We protect your data</p>
                </div>
                <form action="" method="post" class="login-form" id="login-form" onsubmit="handleFormSubmit(event, 1);">
                    <div class="login-form-brand">
                        <h1 class="login-form-title">Login</h1>
                        <?php
                            htmlFormatter::printImage('login.gif', true, [
                                "class" => "login-form-logo"
                            ]);
                        ?>
                    </div>
                    <input type="text" placeholder="Username or mail" class="app-input login-input" name="user_name">
                    <div class="app-password-box">
                            <input type="password" placeholder="Password" class="app-input login-input" name="user_password" id="user_password">
                            <?php
                                htmlFormatter::printImage('eye.png', true, ["onclick" => "showPassword();"]);
                            ?>
                    </div>
                    <div>
                        <div class="app-check-combo login-check-combo">
                            <input type="checkbox" name="extended">
                            <p>Keep logged</p>
                        </div>
                    </div>
                    <input type="submit" value="Login" class="app-input login-input login-submit">
                </form>
            </div>
        </main>
    </body>
    <?php
        htmlFormatter::printScriptLink('login.js', true, true);
    ?>
    </html>