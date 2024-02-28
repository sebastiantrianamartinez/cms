<?php

    (!defined('ROOT')) ? define('ROOT', dirname(__FILE__, 3)) : "";
    require_once ROOT . '/vendor/autoload.php';
    require_once ROOT . '/vendor/ezyang/htmlpurifier/library/HTMLPurifier.auto.php';

    interface SanitizerInterface {
        public function sanitizeHtml($html);
        public function removeSpecialCharacters($text, array $allowedCharacters, bool $default = true);
    }

    class Sanitizer implements SanitizerInterface {
        private $config;

        public function __construct() {
            $this->config = HTMLPurifier_Config::createDefault();
            $this->config->set('HTML.Allowed', '');
        }

        public function sanitizeHtml($html) {
            $purifier = new HTMLPurifier($this->config);
            return $purifier->purify($html);
        }

        public function removeSpecialCharacters($text, array $allowedCharacters, bool $default = true) {
            if($default){
                $defaultAllowed = 'a-zA-Z0-9';
            }
            $allowedCharacters = $defaultAllowed . implode('', array_map('preg_quote', $allowedCharacters));
            $pattern = '/[^' . $allowedCharacters . '\s]/u';
            $text = preg_replace($pattern, '', $text);
            return $text;
        }

        public function removeSpaces($text){
            $sanitizedText = str_replace(["\r", "\n", "\t", ' '], '', $text);
            return $sanitizedText;
        }
        
    }