<?php
    (!defined('ROOT')) ? define('ROOT', dirname(__FILE__, 3)) : "";
    require_once ROOT .'/core/excp/throwing.php';

    class routing {
        public static function library(string $module, $submodule){
            $excents = routing::config('routing', NULL)["data"]["excents"];
            $file = ROOT .'/core/' .$module .'/';
            if(array_key_exists($module, $excents)){
                $file = ROOT .'/' .$module .'/';
            }
            $file .= ($submodule === NULL) ? $module .'.php' : $submodule .'.php';
            if(!file_exists($file)){
                return ["status" => 404, "message"=> "Module does not exists ", "data"=>NULL];
            }
            require_once $file;
            return ["status" => 200, "message"=> "Module online", "data"=>NULL];
        }

        public static function config(string $module, $submodule) {
            $file = ROOT .'/config/' .$module .'/';
            $file .= ($submodule === NULL) ? 'config.json' : $submodule .'.json';
            if(!file_exists($file)){
                return ["status" => 404, "message"=> "Config file does not exists ", "data"=>NULL];
            }
            $config = json_decode(file_get_contents($file), true);
            return ["status" => 200, "message"=> "Config online", "data"=>$config];
        }
        
        public static function key(string $keyName) {
            $file = ROOT .'/config/keys/' .$keyName .'.key';
            if(!file_exists($file)){
                return ["status" => 404, "message"=> "Key file does not exists ", "data"=>NULL];
            }
            $key = file_get_contents($file);
            return ["status" => 200, "message"=> "Key online", "data"=>$key];
        }

        public static function bigRouting(array $modules){
            $failure = false;
            $exceptions = array();
            foreach($modules as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $val) {
                        if (!is_string($val)) {
                            $exceptions[$key] = $val;
                            $failure = true;
                        } else {
                            $process = routing::library($key, $val);
                            if($process["status"] != 200){
                                $exceptions[$key] = $val;
                                $failure = true;
                            }
                        }
                    }
                } 
                else {
                    $process = routing::library($key, $value);
                    if($process["status"] != 200){
                        $exceptions[$key] = $value;
                        $failure = true;
                    }
                }
            }
            if($failure == false){
                return ["status" => 200, "message"=> "All modules online", "data"=>NULL];
            }
            return ["status" => 400, "message"=> "Some modules connection failed", "data"=>$exceptions];
        }
        
    }
?>