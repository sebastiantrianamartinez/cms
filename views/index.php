<?php
    (!defined('ROOT')) ? define('ROOT', dirname(__FILE__, 2)) : "";
    require_once ROOT .'/waf/ipAccess.php';

    $IpAccess = new IpAccess();

    if($IpAccess->isIpBlocked($_SERVER["REMOTE_ADDR"])){
        echo '403 usted ha sido bloqueado';
    }
    else{
        echo '200 bienvenido al sistema';
    }