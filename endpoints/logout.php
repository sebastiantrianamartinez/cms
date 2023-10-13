<?php
    (!defined('ROOT')) ? define('ROOT', dirname(__FILE__, 2)) : "";
    require_once ROOT .'/core/routing/routing.php';

    $modules = [
        'auth' => 'authentication'
    ];

    routing::bigRouting($modules);

    $authentication = new authentication();
    $authentication->deleteSession();

    $website = routing::config('project', 'dns')["data"]["website"];

    header('location: ' .$website);
?>