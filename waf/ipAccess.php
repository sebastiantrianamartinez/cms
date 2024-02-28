<?php

    (!defined('ROOT')) ? define('ROOT', dirname(__FILE__, 2)) : "";
    require_once ROOT . '/core/routing/routing.php';
    require_once ROOT . '/vendor/autoload.php';
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;

    class IpAccess {
        private $blockedIps = [];

        public function __construct() {
            $this->loadBlockedIps();
        }

        public function isIpBlocked($ip = null) {
            $ip = (!is_null($ip)) ? $ip : $_SERVER["REMOTE_ADDR"];
            return isset($this->blockedIps[$ip]);
        }

        public function blockIp($ip = null, $cause = 'Unknown') {
            $ip = (!is_null($ip)) ? $ip : $_SERVER["REMOTE_ADDR"];
            if (!$this->isIpBlocked($ip)) {
                $this->blockedIps[$ip] = $cause;
                $this->saveBlockedIps();
            }
        }

        public function unblockIp($ip) {
            if ($this->isIpBlocked($ip)) {
                unset($this->blockedIps[$ip]);
                // Guardar las IPs bloqueadas en el archivo CSV
                $this->saveBlockedIps();
            }
        }

        public function checkAccess(Request $request) {
            $ip = $request->getClientIp();

            if ($this->isIpBlocked($ip)) {
                // Acceso bloqueado, puedes redirigir o responder según tus necesidades
                $cause = $this->blockedIps[$ip];
                return new Response("Access Denied: Your IP ($ip) is blocked. Cause: $cause", 403);
            }

            // Acceso permitido
            return null;
        }

        private function loadBlockedIps() {
            
            $csvFilePath = ROOT . '/waf/storage/blacklist.csv';

            if (file_exists($csvFilePath)) {
                $csvFile = fopen($csvFilePath, 'r');

                while (($data = fgetcsv($csvFile)) !== false) {
                    $ip = $data[0];
                    $cause = $data[1];
                    $this->blockedIps[$ip] = $cause;
                }

                fclose($csvFile);
            }
        }

        private function saveBlockedIps() {
            $csvFilePath = ROOT . '/waf/storage/blacklist.csv';

            $csvFile = fopen($csvFilePath, 'w');

            foreach ($this->blockedIps as $ip => $cause) {
                fputcsv($csvFile, [$ip, $cause]);
            }

            fclose($csvFile);
        }
    }
?>