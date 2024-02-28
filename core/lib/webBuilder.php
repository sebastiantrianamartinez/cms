<?php
    class WebBuilder {

        protected $website;
        protected $imagesPath;
        protected $scriptsPath;
        protected $stylesPath;

        public function __construct() {
            $config = Routing::config("lib", "webbuilder");
            $projectConfig = Routing::config("project");
            $this->website = $projectConfig["website"];
            $this->imagesPath = $config["images"];
            $this->scriptsPath = $config["scripts"];
            $this->stylesPath = $config["styles"];
        }

        public function getImage($imageName, $attributes = []) {
            $imageSrc = $this->combinePaths($this->website, $this->imagesPath, $imageName);
            return $this->createElement('img', array_merge(['src' => $imageSrc], $attributes));
        }

        public function getScript($scriptName, $attributes = []) {
            $scriptSrc = $this->buildAssetPath($scriptName, $this->scriptsPath);
            return $this->createElement('script', array_merge(['src' => $scriptSrc], $attributes));
        }
        
        public function getStyle($styleName, $attributes = []) {
            $styleHref = $this->buildAssetPath($styleName, $this->stylesPath);
            return $this->createElement('link', array_merge(['rel' => 'stylesheet', 'href' => $styleHref], $attributes));
        }
        
        

        public function getLink($url, $text, $attributes = []) {
            $absoluteUrl = $this->combinePaths($this->website, $url);
            return $this->createElement('a', array_merge(['href' => $absoluteUrl], $attributes), $text);
        }

        public function getFavicon($iconPath, $attributes = []) {
            $absolutePath = $this->combinePaths($this->website, $this->imagesPath, $iconPath);
            return $this->createElement('link', array_merge(['rel' => 'icon', 'href' => $absolutePath], $attributes));
        }

        public function getBootstrapCSS($attributes = []) {
            $bootstrapPath = 'https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css';
            return $this->createElement('link', array_merge(['rel' => 'stylesheet', 'href' => $bootstrapPath], $attributes));
        }

        public function getBootstrapJS($attributes = []) {
            $bootstrapPath = 'https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js';
            return $this->getScript($bootstrapPath, array_merge(['integrity' => '...', 'crossorigin' => 'anonymous'], $attributes));
        }

        public function getJQuery($attributes = []) {
            $jqueryPath = 'https://code.jquery.com/jquery-3.6.4.min.js';
            return $this->getScript($jqueryPath, array_merge(['integrity' => '...', 'crossorigin' => 'anonymous'], $attributes));
        }

        protected function combinePaths(...$paths) {
            return implode('/', array_map(function ($path) {
                return rtrim($path, '/');
            }, $paths));
        }

        protected function createElement($tagName, $attributes, $text = '') {
            $html = '<' . $tagName;
            foreach ($attributes as $name => $value) {
                $html .= ' ' . $name . '="' . htmlspecialchars($value) . '"';
            }
            $html .= '>' . $text . '</' . $tagName . '>';
            return $html;
        }

        private function buildAssetPath($fileName, $basePath) {
            if (filter_var($fileName, FILTER_VALIDATE_URL) !== false) {
                return $fileName;
            }
        
            return $this->website . $basePath . '/' . $fileName;
        }
    }
?>
