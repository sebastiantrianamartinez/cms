<?php
    class stringUtility {
        const LOWERCASE_ONLY = '/^[a-z]*$/';
        const UPPERCASE_ONLY = '/^[A-Z]*$/';
        const LETTERS_ONLY = '/^[a-zA-Z]*$/';
        const LETTERS_AND_NUMBERS = '/^[a-zA-Z0-9]*$/';
        const EMAIL_ADDRESS = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
    
        public static function isStringValid($input, $pattern = null, $excludedChars = null, $minLength = null) {
            if ($pattern !== null && preg_match($pattern, $input)) {
                if ($minLength === null || strlen($input) >= $minLength) {
                    return true;
                }
            }
    
            if ($excludedChars !== null && is_array($excludedChars)) {
                $excludedCharsPattern = '/[^' . preg_quote(implode('', $excludedChars), '/') . ']/';
                if (!preg_match($excludedCharsPattern, $input)) {
                    if ($minLength === null || strlen($input) >= $minLength) {
                        return true;
                    }
                }
            }
            return false;
        }
        public static function isStringLengthValid($input, $minLength, $maxLength) {
            $inputLength = strlen($input);
            return ($inputLength >= $minLength && $inputLength <= $maxLength);
        }
    }
    
?>