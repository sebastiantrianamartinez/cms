<?php
    
    (!defined('ROOT')) ? define('ROOT', dirname(__FILE__, 2)) : "";
    require_once ROOT .'/core/routing/routing.php';

    $models = [
        "lib" => ["exception"]
    ];

    Routing::vendor();
    Routing::waf('ipAccess');
    Routing::waf('ruler');
    Routing::waf('waflogger');
    Routing::waf('integrity/identifier');
    Routing::waf('integrity/sanitizer');
    Routing::model(null, $models);

    use Core\Database\Entities\Services;
    use Core\Database\Entities\Users;
    use Core\Database\Entities\Rules;

    class Waf {
        private $entityManager;
        private $ipAccess;
        private $config;
        private $waflogger;
        private $ruler;

        public function __construct($entityManager){
            $this->entityManager = $entityManager;
            $this->ipAccess = new IpAccess();
            $this->config = Routing::config('waf');
            $this->waflogger = new waflogger($this->entityManager);
            $this->ruler = new Ruler($this->entityManager);
        }
        

        // ¡ PREVENTION MANAGEMENT
        public function serviceMiddleware(Users $user = null, Services $service = null) {
            if($this->ipAccess->isIpBlocked()){
                $this->waflogger->create($user, $service, 10, 11, null, null);
                throw new EnException("IpAccess match", 4012, null, null);
            }
            $rules = $this->ruler->getMatchs($user, $service);
            if(!empty($rules)){
                $this->waflogger->create($user, $service, 10, 10, null, null);
                $matches = $this->attempting($user, $service);
                throw new EnException("Drop rule match", 4012, null, $rules[0]->getExp());
            }
              
        }

        // ¡ ACTION MANAGEMENT
        public function violationProtocol(Users $user = null, Services $service = null, int $code){
            $violationIndex = "CWE-" .(string) $code;
            $violation = $this->config["violations"][$violationIndex];
            if(!is_array($violation)){
                $violation = $this->config["violations"]["default"];
            }
            $matchs = $this->waflogger->getMatchs(null, $code, $violation["effect"])[1];
            $sanction = $this->config["sanctions"][$violation["sanction"]][$matchs];
            if(is_int($sanction)){
                echo $this->ruler->create($user, null, $code, null, null, $sanction)->getId() .' -- ' .$sanction;
            }
        }

        public function tracking(Users $user = null, Services $service = null){
            $effect = $this->config["policy"]["tracking_effect"];
            $serviceLevel = (is_null($service)) ? 1 : $service->getLevel();
            $rpt = $this->config["policy"]["level_rpm"][$serviceLevel];
            $interval = $this->config["policy"]["tracking_interval"];

            $matchs = $this->waflogger->getMatchs($user, null, $effect, false);

            if($matchs[1] >= $rpt){
                $timeGroups = []; 
                foreach($matchs[0] as $match){
                    $timestamp = $match->getIat()->getTimestamp();
                    $groupKey = floor($timestamp / $interval); 
                    if (!isset($timeGroups[$groupKey])) {
                        $timeGroups[$groupKey] = [];
                    }
                    $timeGroups[$groupKey][] = $timestamp;
                }

                $requestCounts = array_map('count', $timeGroups);
                $highRequestGroups = array_filter($requestCounts, function ($count) {
                    return $count >= $rpt;
                });
                
                if(!empty($highRequestGroups)){
                    echo $this->ruler->create($user, $service, 10, 10, null, null, false)->getId();
                }
            }
        }

        public function attempting(Users $user = null, Services $service = null){
            $violationIndex = "SWE-10";
            $violation = $this->config["violations"][$violationIndex];
            $effect = $this->config["policy"]["attempting_effect"];
            $matchs = $this->waflogger->getMatchs($user, $service, 10, 3600, false);
            if($matchs[1] > 12){
                $this->ipAccess->blockIp($ip, $violation["message"]);
            }
            return $matchs;
        }

        public function authFail(Services $service){
            $matchs = $this->waflogger->getMatchs(null, $service, 2, 3600, false);
            $attempts = $matchs[1];
            
            if(isset($this->config["sanctions"]["1"][$attempts])){
                $sanction = $this->config["sanctions"]["1"][$attempts];
                $sanction = ($sanction > 0) ? $sanction / 60 : -1;
                $this->ruler->create(null, $service, 1, 352, null, $sanction, null);

                $format = "d/m/Y H:i:s";
                $lockedUp = date($format, time() + $sanction);

                return 'User suspended to: ' .$lockedUp; 
            }
            foreach($this->config["sanctions"]["1"] as $critic => $value){
                if($critic > $attempts){
                    return $critic - $attempts ." attempts reaming";
                }
            }
            return "nothing";
        }
    }
    