<?php
    (!defined('ROOT')) ? define('ROOT', dirname(__FILE__, 3)) : "";
    require_once ROOT .'/core/routing/routing.php';
    
    Routing::vendor();
    $config = Routing::config("database");

    use Doctrine\ORM\Tools\Setup;
    use Doctrine\ORM\EntityManager;
    
    $isDevMode = $config['is_dev_mode'];
    $entitiesPath = $config['entities_path'];

    $paths = is_array($entitiesPath) ? $entitiesPath : [$entitiesPath];
    $dbConfig = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode, null, null, false);
    
    $conn = [
        'driver'   => $config['connection']['driver'],
        'host'     => $config['connection']['host'],
        'dbname'   => $config['connection']['dbname'],
        'user'     => $config['connection']['user'],
        'password' => $config['connection']['password'],
    ];

    try{
        return EntityManager::create($conn, $dbConfig);
    }
    catch(Exception $e){
        throw $e;
    }
    