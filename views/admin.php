<?php
    $sid = 4;

    (!defined('ROOT')) ? define('ROOT', dirname(__FILE__, 2)) : "";
    require_once ROOT .'/views/core.php';

    $models = ["lib" => "webBuilder"];
    Routing::model(null, $models);

    $wb = new WebBuilder();
?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin</title>

        <?php
            echo $wb->getStyle('normalize.css');
            echo $wb->getStyle('admin.css');
            
            echo $wb->getScript('config.js');
            echo $wb->getScript('admin.js');
        ?>
    </head>
    <body>
        <header>

        </header>
        <main>
            <section class="admin-left-bar">
                <div class="admin-left-bar-item" onclick="adminSectionChange('admin/home.php')">
                    <?php echo $wb->getImage('icons/home.png');?>
                    <h3>Escritorio</h3>
                </div>
                <div class="admin-left-bar-item" onclick="adminSectionChange('admin/article.php')">
                    <?php echo $wb->getImage('icons/articles.png');?>
                    <h3>Artículos</h3>
                </div>
            </section>
            <section class="admin-console" id="admin-console">
                <?php Routing::view(null, 'admin/home.php', false); ?>
            </section>
        </main>
    </body>
    </html>