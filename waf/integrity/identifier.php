<?php

    (!defined('ROOT')) ? define('ROOT', dirname(__FILE__, 3)) : "";
    require_once ROOT . '/core/routing/routing.php';
    require_once ROOT . '/vendor/autoload.php';

    use Phpml\Classification\SVC;
    use Phpml\FeatureExtraction\TokenCountVectorizer;
    use Phpml\Tokenization\WhitespaceTokenizer;
    use Phpml\ModelManager;
    use Phpml\Wrapper\Serializable;

    interface identifierInterface{
        //public function identifyRisks(string $data = null, array $dataArray = null): array;
    }

    class Identifier implements identifierInterface {

        protected $vectorizer; 
        protected $modelManager;

        public function __construct(){
            $serializedData = file_get_contents(ROOT .'/config/integrity_vector.ser'); 
            $this->vectorizer = unserialize($serializedData);
            $modelManager = new ModelManager();
            $this->classifier = $modelManager->restoreFromFile(ROOT .'/config/integrity.model');
        }

        public function identifyRisks(string $data = null, array $dataArray = null){
            $evaluation = array();
            $testData = array();

            if(is_string($data) && !is_array($dataArray)){
                $dataArray = [$data];
            }
            foreach($dataArray as $data){
                array_push($testData, strval($data));
                if(strlen($data) < 1 || empty($data)){
                    throw new InvalidArgumentException("Some data is empty");
                } 
            }
            $aiEvaluation = $this->aiDetection($testData);
            $matchEvaluation = $this->matchDetection($testData);
            if($aiEvaluation[1] || $matchEvaluation[1]){
                $i = 0;
                foreach($dataArray as $index => $data){
                    $evaluation[$i] = ($aiEvaluation[0][$i] + $matchEvaluation[0][$i] > 0) ? 1 : 0;
                    $i++;
                }
                return $evaluation;
            }
            return $aiEvaluation[0];
        }

        protected function aiDetection($data){
            $unMapedData = $data;

            $this->vectorizer->transform($data);
            $predictions = $this->classifier->predict($data);

            $answer = array();
            $alert = false;
            $i = 0;

            foreach ($predictions as $index => $prediction) {
                if(!$this->checkDataLenght($unMapedData[$i])){
                    $answer[$i] = 0;
                    break;
                }
                if(!$alert && boolval($prediction)){
                    $alert = true;
                }
                $answer[$i] = $prediction;
                $i++;
            }
            return [$answer, $alert];
        }

        protected function matchDetection($data){
            $xssPattern = Routing::config('integrity', null);
            $answer = array();
            $alert = false;
            $i = 0;
            
            foreach($data as $text){
                if($this->checkDataLenght($text) < 3){
                    break;
                }
                $prediction = 0;
                $patternA = (strpos($text, '<script>') || strpos($text, '</script>')) ? true : false;
                $patternB = ((strpos($text, 'src="') || strpos($text, "src='") ) && strpos($text, '</script>')) ? true : false;
                foreach ($xssPattern as $pattern) {
                    $tagPresent = (strpos($text, $pattern) !== false);
                    $closingBracketPresent = (strpos($text, '>', strpos($text, $pattern)) !== false);
                    $patternC = ($tagPresent && $closingBracketPresent);
                    if ($patternC) {
                        break;
                    }
                }
            
                if($patternB || $patternA || $patternC){ 
                    $prediction = 1;
                    if(!$alert){
                        $alert = true;
                    }
                }

                $answer[$i] = $prediction;
                $i++;
            }
            return [$answer, $alert];
        }

        protected function checkDataLenght($data){
            $validation = (strlen($data) > 2);
            return $validation;
        }
    }