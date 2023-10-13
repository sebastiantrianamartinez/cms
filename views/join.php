<?php
    (!defined('ROOT')) ? define('ROOT', dirname(__FILE__, 2)) : "";
    require_once ROOT .'/core/routing/routing.php';

    $service = [
        "id" => 3
    ];
    require_once ROOT .'/auth/headers/interface_header.php';

    if(is_int($user["id"]) && $user["id"] > 0){
        responser::httpResponse(400, 'You have an active session, please logout first', NULL);
    }

    $modules = [
        "lib" => "htmlFormatter",
    ];
    routing::bigRouting($modules);

?>
    <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Join</title>
            <?php
                htmlFormatter::printFaviconLink('favicon.ico', true);

                htmlFormatter::printStylesheetLink('normalize.css', true);
                htmlFormatter::printStylesheetLink('join.css', true);

                htmlFormatter::printScriptLink('config.js', true, true);
                htmlFormatter::printScriptLink('app/forms.js', true, true);
            ?>
        </head>
        <body>
            <main>
                <div class="join-form-section app-form-box">
                    <div class="app-form-info-box">
                        <p></p>
                    </div>
                    <form action="" class="app-vertical-form form" id="join-form" onsubmit="handleFormSubmit(event, 3);"> 
                        <div class="username-box app-password-box">
                            <input type="text" placeholder="username" class="app-input" required id="user_name" name="user_name">
                            <?php
                                htmlFormatter::printImage('ok.png', true, ["id" => "username-status-img", "class" => "username-status"]);
                            ?>
                        </div>
                        <p class="username-info" id="username-info">Username available</p>
                        
                        <input type="text" placeholder="Full name" class="app-input" required name="user_alias">
                        <input type="email" placeholder="Mail" class="app-input" required name="user_mail">
                        <div class="app-password-box">
                            <input type="password" placeholder="Password" class="app-input" id="user_passwordA" required name="user_password"> 
                            <?php
                                htmlFormatter::printImage('eye.png', true, ["onclick" => "showPassword(this, 'user_passwordA');", "id" => "show-password-imageA"]);
                            ?>
                        </div>
                        <div class="app-password-box">
                            <input type="password" placeholder="Confirm password" class="app-input" id="user_passwordB" required name="user_password_confirm">
                            <?php
                                htmlFormatter::printImage('eye.png', true, ["onclick" => "showPassword(this, 'user_passwordB');", "id" => "show-password-imageB"]);
                            ?>
                        </div>
                        <div class="app-check-combo login-check-combo">
                            <input type="checkbox" checked>
                            <p>I accept almost all <a href="<?php echo $website .'/info/policy';?>" target="blank" class="app-link">terms and conditions</a></p>
                        </div>
                        <input type="submit" value="Create account" class="app-input join-submit">
                    </form>
                </div>
            </main>
            
        </body>
        <?php
            htmlFormatter::printScriptLink('join.js', true, true);
        ?>
    </html>