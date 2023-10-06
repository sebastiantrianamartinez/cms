<?php
    (!defined('ROOT')) ? define('ROOT', dirname(__FILE__, 2)) : "";
    require_once ROOT .'/core/routing/routing.php';

    $modules = [
        "lib" => "htmlFormatter",
    ];
    routing::bigRouting($modules);

    echo 'INDEX'
?>