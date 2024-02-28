<?php
    
    (!defined('ROOT')) ? define('ROOT', dirname(__FILE__, 3)) : "";
    require_once ROOT . '/vendor/autoload.php';

    use Doctrine\Common\Cache\Cache;
    use Doctrine\Common\Cache\ArrayCache;
    use Doctrine\Common\Cache\FilesystemCache;
    use Doctrine\Common\Cache\MemcachedCache;
   
    class CacheExpirer implements FilesystemCache {
        public function is_expired($id){
            if(is_string(doFetch($id))){
                return false;
            }
            else {
                return true;
            }
        }
    }