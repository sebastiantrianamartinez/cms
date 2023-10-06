<?php
    (!defined('ROOT')) ? define("ROOT", dirname(__FILE__, 3)) : "";
    require_once ROOT . '/core/routing/routing.php';
    
    $modules = [
        "lib" => "responser"
    ];
    
    routing::bigRouting($modules);

    interface CacheInterface {
        public function get($key);
        public function set($key, $value, $ttl = 3600);
        public function delete($key);
    }

    class FileCache implements CacheInterface {
        
        private $config;
        private $cacheDir;
    
        public function __construct() {
            $this->config = routing::config('caching', NULL)["data"]; 
            $this->cacheDir = ROOT .$this->config["dir"];
        }
    
        public function get($key) {
            $cacheFile = $this->cacheDir . '/' . md5($key);
            if (file_exists($cacheFile) && is_readable($cacheFile)) {
                $data = file_get_contents($cacheFile);
                return responser::systemResponse(200, "success cache requestion", unserialize($data));
            }
            return responser::systemResponse(400, "unsuccess cache requestion", NULL);
        }
    
        public function set($key, $value, $ttl = 3600) {
            $cacheFile = $this->cacheDir . '/' . md5($key);
            $data = serialize($value);
            file_put_contents($cacheFile, $data, LOCK_EX);
            touch($cacheFile, time() + $ttl);
            return responser::systemResponse(200, "success cache information seted", NULL);
        }
    
        public function delete($key) {
            $cacheFile = $this->cacheDir . '/' . md5($key);
            if (file_exists($cacheFile)) {
                unlink($cacheFile);
            }
            return responser::systemResponse(200, "success cache information deleted", NULL);
        }
    }
    
