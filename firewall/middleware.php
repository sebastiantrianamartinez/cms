<?php
    (!defined('ROOT')) ? define('ROOT', dirname(__FILE__, 2)) : "";
    require_once ROOT . '/core/routing/routing.php';
    
    $timezone = routing::config('project', 'dns')["data"]["timezone"];
    date_default_timezone_set($timezone);
    
    $modules = [
        "lib" => "responser"
    ];
    
    routing::bigRouting($modules);
    
    class middleware {
        public function sanitizeArrayRecursive($inputArray) {
            $attacksDetected = [];
        
            foreach ($inputArray as $key => &$value) {
                if (is_array($value)) {
                    // If the element is an array, recursively sanitize it
                    $sanitizedValue = sanitizeArrayRecursive($value);
                    if ($sanitizedValue['status'] === 400) {
                        // An attack was detected in a nested array
                        $attacksDetected[$key] = $sanitizedValue['data'];
                    } else {
                        $value = $sanitizedValue['data'];
                    }
                } elseif (is_string($value)) {
                    // If the element is a string, check and sanitize for SQL and JavaScript injection
                    if ($this->detectSqlInjection($value)) {
                        // Handle SQL injection
                        $attacksDetected[$key] = ['element' => $key, 'attack' => 'SQL injection'];
                        $value = $sanitizedValue;
                    }
                    
                    if ($this->detectXssInjection($value)) {
                        // Handle JavaScript (XSS) injection
                        $attacksDetected[$key] = ['element' => $key, 'attack' => 'XSS injection'];
                        $value = $sanitizedValue;
                    }
                }
            }
        
            if (!empty($attacksDetected)) {
                return responser::systemResponse(400, 'Attacks detected in input data', $attacksDetected);
            }
        
            return responser::systemResponse(200, 'Data sanitized successfully', NULL);
        }
        

        private function detectSqlInjection($input) {
            // Common patterns used in SQL injection attacks
            $a=0; $b=0; $c=0; $d=0; $e=0; $f=0;
            $sqlPatterns = [
                "/\bUNION\b/i",
                "/\bSELECT\b/i",
                "/\bINSERT\b/i",
                "/\bUPDATE\b/i",
                "/\bDELETE\b/i",
                "/\bFROM\b/i",
                "/\bWHERE\b/i",
                "/\bDROP\b/i",
                "/\bALTER\b/i",
                "/\bCREATE\b/i",
                "/\bTRUNCATE\b/i",
                "/\bEXEC\b/i",
                "/\bDECLARE\b/i",
                "/\bDATABASE\b/i",
                "/\b--\b/",
                "/\/\*/",
                "/\bCONVERT\b/i",
                "/\bCAST\b/i",
                "/\bTABLE\b/i",
                "/\b%27\b/i",
                "/\b%20\b/i"
            ];
        
            foreach ($sqlPatterns as $pattern) {
                if (preg_match($pattern, $input)) {
                    $a ++;
                }
            }
        
            // Additional check for quotes, as they are common in SQL injections
            if (strpos($input, "'") !== false || strpos($input, "\"") !== false || strpos($input, "';") !== false) {
                $b ++;
            }
            if (strpos($input, "=") !== false) {
                $d ++;
            }
        
            // Additional check for common SQL keywords
            $sqlKeywords = [
                " OR ",
                " AND ",
                " WHERE ",
                " LIMIT ",
                " OFFSET ",
                " ORDER BY ",
                " GROUP BY ",
            ];
        
            foreach ($sqlKeywords as $keyword) {
                if (stripos($input, $keyword) !== false) {
                    $c ++;
                }
            }
            if(strpos($input, " OR " )){
                $e++;
            }
            if(strpos($input, "%27") && strpos($input, "%20")){
                $f++;
            }

            if(($a*$c*$d) > 0 || ($b*$c*$d) > 0 || $a > 2 || ($d*$e) > 0 || (($a*$b) > 0 && $a > 1) || $f > 0){
                return true;
            }
            return false; // No SQL injection patterns were found
        }
        
        private function detectXssInjection($input) {
            // Common patterns used in XSS attacks
            $xssPatterns = [
                "/<script(.*)<\/script>/i",
                "/<\s*img[^>]+onerror\s*=\s*['\"]?[^>]*>/i",
                "/<\s*a[^>]+href\s*=\s*['\"]?(javascript\s*:\s*[^>]*|data\s*:[^>]*)['\"]?[^>]*>/i",
                "/<\s*iframe[^>]*>/i",
                "/<\s*form[^>]*>/i",
                "/<\s*style[^>]*>/i",
                "/<\s*meta[^>]*>/i",
                "/<\s*object[^>]*>/i",
                "/<\s*embed[^>]*>/i",
                "/<\s*frame[^>]*>/i",
                "/<\s*link[^>]+href\s*=\s*['\"]?[^>]*>/i",
                "/<[^>]*>/i" // Para permitir etiquetas HTML sin atributos
            ];
            
            
        
            foreach ($xssPatterns as $pattern) {
                if (preg_match($pattern, $input)) {
                    return true; // A potential XSS pattern was found
                }
            }
        
            return false; // No XSS patterns were found
        }
    }