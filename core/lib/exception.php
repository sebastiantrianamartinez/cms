<?php

    interface enExceptionsInterface {
        public function __construct($message, $code = 0, Exception $previous = null, $metadata = null);
    }

    class EnException extends Exception {
        
        protected $metadata;
    
        public function __construct($message, $code = 0, Exception $previous = null, $metadata = null) {
            parent::__construct($message, $code, $previous);
            $this->metadata = $metadata;
        }
    
        public function getMetadata() {
            return $this->metadata;
        }
    }
?>