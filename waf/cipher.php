<?php
    (!defined('ROOT')) ? define('ROOT', dirname(__FILE__, 2)) : "";
    require_once ROOT .'/core/routing/routing.php';

    $models = [
        "lib" => "exception"
    ];

    Routing::model(null, $models, 'r_o');


    class Cipher {
        private $alphabets; 
        private $alpKeys;

        public function __construct(){
            $this->alphabets = [
                ['f','g','j','i','o','u','w','h','v','x','y','p','t','k','r','z','b','c','a','d','e','q','n','s','m','l'],
                ['n','r','k','p','v','f','g','j','i','o','u','w','h','x','y','c','a','d','e','q','b','t','z','s','m','l'],
                ['k','p','v','f','g','j','i','o','u','w','h','x','y','c','a','d','e','q','b','n','r','t','z','s','m','l'],
                ['p','v','f','g','j','i','o','u','w','h','x','y','c','a','d','e','q','b','n','r','k','t','z','s','m','l'],
                ['v','f','g','j','i','o','u','w','h','x','y','p','t','k','r','z','b','c','a','d','e','q','n','s','m','l']
            ];
            $this->alpKeys = [
                ['c','h','p','t','5'],
                ['s','k','r','y','2'],
                ['i','w','o','b','9'],
                ['l','e','a','q','6'],
                ['m','f','x','d','1']
            ];
        }
        
        public function encode($id, array $offsets = null){
            $encoded = '';
            $left = '';
            $right = '';
        
            $idDigits = array_map('intval', str_split((string)$id));
            $userAlphabetIndex = rand(0, 4);
            $userAlphabet = $this->alphabets[$userAlphabetIndex];
            $userAlphabetKey = $this->alpKeys[$userAlphabetIndex][rand(0, 4)];
        
            foreach($idDigits as $digit){
                $encoded .= $userAlphabet[$digit];
            }
            
            if(isset($offsets)){
                $left = $this->generateRandomString($offsets[0], $this->alphabets[rand(0, 4)]);
                $right = $this->generateRandomString($offsets[1], $this->alphabets[rand(0, 4)]);
            }
            
            return $userAlphabetKey . $left . $encoded . $right;
        }
        
        public function decode($encodedValue, array $offsets){
            $userAlphabetKey = $encodedValue[0];
            $userAlphabetIndex = null;
           
            foreach ($this->alpKeys as $key => $alpKey) {
                if (is_int(array_search($userAlphabetKey, $alpKey))) {
                    $userAlphabetIndex = $key;
                    break;
                }
            }
            
            if ($userAlphabetIndex !== null) {
                $userAlphabet = $this->alphabets[$userAlphabetIndex];
                $decoded = '';
                $encodedDigits = str_split(substr($encodedValue, $offsets[0] + 1, -$offsets[1]));
        
                foreach ($encodedDigits as $letter) {
                    $decoded .= array_search($letter, $userAlphabet);
                }
        
                return (int) $decoded;
            } else {
                throw new EnException("Alphabet index not found", 400, null, null);
                return null;
            }
        }

        private function generateRandomString($length, $alphabet){
            $randomString = '';
            for ($i = 1; $i <= $length; $i++) {
                $randomString .= ($i % 3 == 0) ? rand(0, 9) : $alphabet[rand(0, 25)];
            }
            return $randomString;
        }
        
        public function hash($content, bool $base64 = null){
            $hashed = hash('ripemd160', $content);
            if($base64){
                $hashed = base64_encode($hashed);
            }
            return $hashed;
        }
        
        public function verifyHash($hashed, $content, bool $base64 = null){
            $decoded = $hashed;
            if($base64){
                $decoded = base64_decode($hashed);
            }
            return $this->hash($content) === $decoded;
        }
    }
    
    
   
    
