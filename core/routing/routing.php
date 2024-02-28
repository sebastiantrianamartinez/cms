<?php
    (!defined('ROOT')) ? define('ROOT', dirname(__FILE__, 3)) : "";
    
    
    interface routingInterface {
        public static function model(string $model = null, array $models = null, string $method = 'r_o');
        public static function config(string $config = null, string $subconfig = null);
        public static function view($middlewareHandler, string $view = null, bool $redirect = false);
    }

    class Routing implements routingInterface {

        private static function takein($file, $method){
            if(!file_exists($file)){
                return false;
            }
            switch($method){
                case 'r_o':
                    require_once $file;
                    break;
                case 'r':
                    require $file;
                    break;
                case 'i_o':
                    include_once $file;
                    break;
                case 'i':
                    include $file;
                    break;
            }
            return true;
        }
        
        private static function match(string $module = null, array $modules = null, string $method, string $resource){
            $error = array();
            if(is_array($modules)){
                foreach($modules as $module => $submodules){
                    if(is_array($submodules)){
                        foreach($submodules as $submodule){
                            $file = ROOT .'/' .$resource .'/' .$module .'/' .$submodule .'.php';
                            if(!self::takein($file, $method)){
                                $error[$file] = 'failed';
                            }
                        }
                    }
                    else{
                        $file = ROOT .'/' .$resource .'/' .$module .'/' .$submodules .'.php';
                        if(!self::takein($file, $method)){
                            $error[$file] = 'failed';
                        }
                    }
                }
            }
            else{
                $file = ROOT .'/' .$resourse .'/' .$module .'/' .$module .'.php';
                if(!self::takein($file, $method)){
                    $error[$file] = 'failed';
                }
            }
            
            return (!empty($error)) ? $error : "All success";
        }

        public static function model(string $model = null, array $models = null, string $method = 'r_o'){
            return self::match($model, $models, $method, 'core');
        }
        
        public static function config(string $config = null, string $subconfig = null){
            if(is_string($subconfig)){
                $file = ROOT .'/config/' .$config .'.' .$subconfig .'.json';
            }
            else{
                $file = ROOT .'/config/' .$config .'.json';
            }
            if(!file_exists($file)){
                return '404';
            }      
            return json_decode(file_get_contents($file), true);
        }

        public static function key(string $key = null, string $subkey = null){
            if(is_string($subkey)){
                $file = ROOT .'/config/keys/' .$key .'.' .$subkey .'.key';
            }
            else{
                $file = ROOT .'/config/keys/' .$key .'.key';
            }
            if(!file_exists($file)){
                throw new Exception("key not found");
            }      
            return file_get_contents($file);
        }

        public static function vendor(){
            if(!file_exists(ROOT .'/vendor/autoload.php')){
                throw new Exception("autoload not found");
            }
            require_once ROOT .'/vendor/autoload.php';
        }

        public static function waf(string $module = null, string $submodule = null){
            if(is_string($submodule)){
                $file = ROOT .'/waf/' .$module .'/' .$submodule .'.json';
            }
            else{
                $file = ROOT .'/waf/' .$module .'.php';
            }
            if(!file_exists($file)){
                throw new Exception("Waf module not found");
            }      
            require_once $file;
        }

        public static function entityManager(){
            return require_once ROOT .'/core/database/connection.php';
        }

        public static function view($middlewareHandler, string $view = null, bool $redirect = false){
            $project = self::config('project');
            $website = $project['website'];
            if(isset($middlewareHandler)){
                if(!$middlewareHandler()){
                    $location = $website .'/views/error/unauthorized.php';
                    header('location: ' .$location);
                    die();
                }
            }
            $project = self::config('project', null);
            $website = $project['website'];
            if($redirect){
                $location = $website .'/views/' .$view;
                header('location: ' .$location);
                die();
            }
            $location = ROOT .'/views/' .$view;
            include $location;
            die();
        }  
    }

    date_default_timezone_set(Routing::config('project')["timezone"]); 
?>
