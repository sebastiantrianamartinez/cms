<?php
   
   (!defined('ROOT')) ? define("ROOT", dirname(__FILE__, 3)) : "";
   require_once ROOT . '/core/routing/routing.php';
   
   (!defined('WEBSITE')) ? define("WEBSITE", routing::config('project', 'dns')["data"]["website"]) : "";
    
   class htmlFormatter{

        public static function printStylesheetLink($file, $isLocal) {
            if ($isLocal) {
                $path = "/frontend/styles/";
                $url = WEBSITE . $path . $file;
            } else {
                $url = $file;
            }
            echo '<link rel="stylesheet" type="text/css" href="' . $url . '">';
        }
        public static function printScriptLink($file, $isLocal, $isFrontend) {
            if ($isLocal) {
                $path = $isFrontend ? "/frontend/scripts/" : "/backend/scripts/";
                if (!$isFrontend) {
                    $path = "/backend/scripts/";
                }
                $url = WEBSITE . $path . $file;
            } else {
                $url = $file;
            }
            echo '<script src="' . $url . '"></script>';
        }
        public static function printImage($file, $isLocal, $meta = []) {
            if ($isLocal) {
                
                $path = "/assets/media/images/";
                $url = WEBSITE . $path . $file;
            } else {
                $url = $file;
            }
        
            $metaHTML = "";
            foreach ($meta as $attribute => $value) {
                $metaHTML .= $attribute . '="' . $value . '" ';
            }
        
            echo '<img src="' . $url . '" ' . $metaHTML . '>';
        }
        public static function printFaviconLink($file, $isLocal) {
            if ($isLocal) {
                
                $path = "/assets/media/images/";
                $url = WEBSITE . $path . $file;
            } else {
                $url = $file;
            }
            echo '<link rel="shortcut icon" type="image/x-icon" href="' . $url . '">';
        }
    }
?>