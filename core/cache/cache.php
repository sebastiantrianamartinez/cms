<?php

    (!defined('ROOT')) ? define('ROOT', dirname(__FILE__, 3)) : "";
    require_once ROOT . '/vendor/autoload.php';

    use Doctrine\Common\Cache\Cache;
    use Doctrine\Common\Cache\ArrayCache;
    use Doctrine\Common\Cache\FilesystemCache;
    use Doctrine\Common\Cache\MemcachedCache;
   
    class CacheManager implements Cache
    {
        protected $cache;

        public function __construct($mode = null, $path = null, $servers = null) {
            $mode = $mode ?: 'file';
            $path = $path ?: ROOT . '/storage/cache';
        
            switch($mode) {
                case 'array':
                    $this->cache = new ArrayCache();
                    break;
                case 'memcached':
                    $memcached = new \Memcached();
                    $memcached->addServers($servers);
        
                    $doctrineMemcached = new MemcachedCache();
                    $doctrineMemcached->setMemcached($memcached);
        
                    $this->cache = $doctrineMemcached;
                    break;
                default:
                    $this->cache = new FilesystemCache($path);
                    break;
            }
        }

        public function fetch($id) {
            return $this->cache->fetch($id);
        }

        public function save($id, $data, $lifeTime = 0) {
            return $this->cache->save($id, $data, $lifeTime);
        }

        public function delete($id) {
            return $this->cache->delete($id);
        }

        public function flush() {
            return $this->cache->flushAll(); 
        }

        public function keyGen($keyName) {
            $key = base64_encode(crc32($keyName . microtime())) .'cAc';
            return $key;
        }

        public function contains($id) {
            return $this->cache->contains($id);
        }

        public function getStats() {
            return $this->cache->getStats();
        }

        public function clean(){
            $keys = $this->getKeys();
            $i = 0;
            foreach ($keys as $index => $key) {
                $data = $this->cache->contains($key);
                if(!$data){
                    $this->cache->delete($key);
                    unset($keys[$index]);
                    $i++;
                }
            }
            $keyString = implode('%', $keys);
            $this->cache->save('k5fe3s2ss6cd', $keyString, 86400);
            return $i .' elements deleted';
        }

        protected function getKeys() {
            $keyString = $this->cache->fetch('k5fe3s2ss6cd');
            $keyArray = explode('%', $keyString);
            return $keyArray;
        }
    }
