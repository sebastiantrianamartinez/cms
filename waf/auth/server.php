<?php
    (!defined('ROOT')) ? define('ROOT', dirname(__FILE__, 3)) : "";
    require_once ROOT .'/core/routing/routing.php';

    $models = [
        "tokenization" => "tokenization",
        "lib" => ["exception"],
        "cache" => "cache"
    ];

    Routing::vendor();
    Routing::waf('cipher');
    Routing::waf('ipAccess');
    Routing::waf('waf');
    Routing::waf('waflogger');
    Routing::model(null, $models);

    use Core\Database\Entities\Users;
    use Core\Database\Entities\Services;
    use Core\Database\Entities\Tokens;
    use Core\Database\Entities\Permissions;

    class AuthServer {

        protected $entityManager;
        private $tokenization;
        private $config;
        private $cipher;
        private $cache;
        private $waflogger;
        private $waf;

        public function __construct($entityManager) {
            $this->entityManager = $entityManager;
            $this->tokenization = new Tokenization();
            $this->config = Routing::config('auth');
            $this->cipher = new Cipher();
            $this->cache = new CacheManager();
            $this->waflogger = new WafLogger($this->entityManager);
            $this->waf = new Waf($this->entityManager);
        }

        public function authenticateTokenize(string $token){
            $payload = $this->tokenization->decodeToken($token);
            $idGap = $this->config["session"]["gaps"]["authenticate_id"];

            if(is_array($payload)){
                $userId = $this->cipher->decode($payload["identity"], $idGap) - $this->config["session"]["id_gap"];
                $userRepository = $this->entityManager->getRepository(Users::class);
                $user = $userRepository->find($userId);
                $tokenRepository = $this->entityManager->getRepository(Tokens::class);
                $dbToken = $tokenRepository->findOneBy(['value' => $token]);
                if(!$dbToken){
                    return false;
                }
                if($payload["exp"] > (time() - $this->config["renovation"])){
                    $tokenExp = $this->config["session"]["roles"][($user->getGroup() > 5) ? "user" : "admin"]["exp"];
                    $payload["exp"] = time() + $tokenExp;
                    $tokenValue = $this->tokenization->newToken($payload);
                    $this->tokenize($tokenValue, $userId, $tokenExp, "update");
                }
                return $user;
            }
        }

        public function authenticate($username, $password){
            //$username = base64_decode($username);
            //$password = base64_decode($password);

            $userRepository = $this->entityManager->getRepository(Users::class);
            $user = $userRepository->findOneBy([
                'name' => $username
            ]);
            
            if(!$user) {
                $user = $userRepository->findOneBy([
                    'mail' => $username
                ]);
                if(!$user) {
                    $this->waflogger->create(null, null, 2, 1, null, $username);
                    throw new EnException("User not found", 4001, null, $username);
                }
            }
            if(!password_verify($password, $user->getPassword())){
                $this->waflogger->create(null, null, 2, 2, null, $user->getId());
                throw new EnException("Password incorrect", 4001, null, $username);
            }
            if($user->getStatus() !== 2 ){
                if($user->getStatus() === 1){
                    $this->waflogger->create(null, null, 2, 3, null, $user->getId());
                    throw new EnException("User isn't active", 4002, null, null);
                }
                $cause = ($user->getStatus() == 3) ? 4 : 5;
                $this->waflogger->create(null, null, 2, $cause, null, $user->getId());
                throw new EnException("User is blocked or doesn't exists", 4003, null, $user->getStatus());
            }
            return $user;
        }

        public function sessionize(Users $user, $persist = true){
            $roleGap = $this->config["session"]["gaps"]["authenticate_role"];
            $idGap = $this->config["session"]["gaps"]["authenticate_id"];

            $tokenExp = $this->config["session"]["roles"][($user->getGroup() > 5) ? "user" : "admin"]["exp"];
            $payload = [
                "iat" => time(),
                "exp" => time() + $tokenExp,
                "name" => $user->getAlias(),
                "identity" => $this->cipher->encode($user->getId() + $this->config["session"]["id_gap"], $idGap),
                "task" => $this->cipher->encode($user->getGroup(), $roleGap),
                "client" => $this->cipher->hash($_SERVER["HTTP_USER_AGENT"])
            ];

            $tokenValue = $this->tokenization->newToken($payload);
            $token = $this->tokenize($tokenValue, $user->getId(), $tokenExp);

            $storeExp = ($persist) ? $tokenExp : null;
            $this->store($tokenValue, $storeExp);

            $this->waflogger->create($user, null, 3, null, null, null);
            return $token;
        }

        private function tokenize($tokenValue, $userId, $exp, $action = null) {
            if(is_null($action)){
                $token = new Tokens();
                $token->setValue($tokenValue);
                $token->setUser($userId);
                $token->setIp();
                $token->setAgent();
                $token->setIat();
                $token->setExp($exp);
                $this->entityManager->persist($token);
            }
            if($action == "update"){
                $tokenRepository = $this->entityManager->getRepository(Tokens::class);
                $token = $tokenRepository->findOneBy(['value' => $currentTokenValue]);
                if ($token) {
                    $token->setValue($tokenValue);
                    $this->store($tokenValue, $exp);
                }
            
            }
            $this->entityManager->flush();
            return $token;
        }

        private function store($tokenValue, $exp = null){
            $cookieName = 'session_token';
            $cookieExp = (is_int($exp)) ? time() + $exp : null;
            setcookie($cookieName, $tokenValue, $cookieExp, '/', ''); //TODO (HTTPS) |  , true, true); 
            
        }

        public function unlink($tokenValue){
            $this->store('', -1000);  
            $tokenRepository = $this->entityManager->getRepository(Tokens::class);
            $token = $tokenRepository->findOneBy(['value' => $tokenValue]);
            if ($token) {
                $user = new Users();

                $user->setId($token->getUser());

                $this->waflogger->create($user, null, 4, null, null, null);
                $this->entityManager->remove($token);
                $this->entityManager->flush();
                return true; 
            }
            return false;
        }

        // AUTHORIZATION -----------------------------------------------

        public function authorizationTokenize(Users $user, Services $service){
            if($service->getStatus() != 2){
                $this->waflogger->create($user, $service, 5, 1, null, null);
                throw new EnException("Service unavailable", 4004, null, null);
            }

            $permissionRepository = $this->entityManager->getRepository(Permissions::class);
            $permission = $permissionRepository->createQueryBuilder('p')
                ->where('p.service = :service')
                ->andWhere('(p.user = :userId OR p.group = :groupId OR p.ip = :currentIp OR p.group = -1)')
                ->setParameters([
                    'service' => $service->getId(),
                    'userId' => $user->getId(),
                    'groupId' => $user->getGroup(),
                    'currentIp' => $_SERVER["REMOTE_ADDR"],
                ])
                ->getQuery()
                ->getResult();
            if(empty($permission)){
                $this->waflogger->create($user, $service, 6, 1, null);
                throw new EnException("User not authorized", 4005, null, null);
            }    
            
            
            $this->waf->serviceMiddleware($user, $service);

            $criticalService = ($service->getLevel() <= 1) ? true : false;
            $exp = $service->getExp();
            $sid = $this->identity();

            $crudGap = $this->config["session"]["gaps"]["authorize_crud"];
            $sidGap = $this->config["session"]["gaps"]["authorize_id"];

            $payload = [
                "iat" => time(),
                "exp" => time() + $exp,
                "uuid" => uniqid(),
                "sid" => $this->cipher->encode($service->getId(), $sidGap),
                "sid_" => $sid,
                "scope" => $this->cipher->encode($permission[0]->getCrud(), $crudGap),
                "client" => $this->cipher->hash($_SERVER["HTTP_USER_AGENT"])
            ];

            $token = $this->tokenization->newToken($payload);
            return $token;
        }

        private function identity(string $method = null){
            session_start();
            $identifier = $this->config["session"]["uniq_id"];

            if($method == 'set' || is_null($method)){
                if(!isset($_SESSION[$identifier])){
                    $sessionId = uniqid("sid_", true);
                    $_SESSION[$identifier] = $sessionId;
                }
                return $_SESSION[$identifier];
            }
            else{
                if(isset($_SESSION[$identifier])){
                    return $_SESSION[$identifier];
                }
                return 'ERROR';
            }
        }

        public function authorize(Users $user, Services $service, string $token, int $action){
            $payload = $this->tokenization->decodeToken($token);
            $crudGap = $this->config["session"]["gaps"]["authorize_crud"];
            $sidGap = $this->config["session"]["gaps"]["authorize_id"];

            // TODO -- START -- CREATE WAF ACTIONS FOR EACH CASE
            if($payload["client"] != $this->cipher->hash($_SERVER["HTTP_USER_AGENT"])){
                $this->waflogger->create($user, $service, 8, 7, null, $action);
                throw new EnException("Client is incorrect", 4007, null, null);
            }
            if($payload["sid_"] != $this->identity("get")){
                $this->waflogger->create($user, $service, 8, 9, null, $action);
                throw new EnException("session is different ", 4008, null, null);
            }
            if($this->cipher->decode($payload["sid"], $sidGap) != $service->getId()){
                $this->waflogger->create($user, $service, 8, 8, null, $action);
                throw new EnException("service_id is incorrect ", 49009, null, null);
            }
            $scope = $this->cipher->decode($payload["scope"], $crudGap);
            if(($scope & $action) !== $action) {
                $this->waflogger->create($user, $service, 8, null, null, $action);
                throw new EnException("User can't do dis action " .$action, 4010, null, null);
            }
            if($this->oneuse(null, $payload["uuid"], 'get')){
                $this->waflogger->create($user, $service, 8, 6, null, $action);
                throw new EnException("Token used before", 4011, null, null);
            }

            // TODO -- END -------

            $timeout = $service->getTimeout();
            if(!is_null($timeout)){
                $waflogger = new Waflogger($this->entityManager);
                $matchs = $waflogger->getMatchs($user, $service, 1, $timeout, false);
                if($matchs[1] > 0){
                    $this->waflogger->create($user, $service, 7, 1, null, null);
                    throw new EnException("Recent transaction detected " .$timeout, 4006, null, $matchs);
                }
            }

            // SERVICE IS ALLOWED FOR CONSUME
            $this->oneuse('token used', $payload["uuid"], 'set', $payload["exp"] - time());
            $newToken = $this->authorizationTokenize($user, $service);
            $this->waflogger->create($user, $service, 1, null, null, $action);
            return $newToken;
        }

        public function oneuse(string $token = null, string $uuid, string $action = null, int $exp = 3600){
            if($action == 'get' || is_null($action)){
                $value = $this->cache->fetch($uuid);
                if(is_string($value)){
                    return true;
                }
                return false;
            }
            else{
                $keys = "";
                $keys = $this->cache->fetch('keys');
                $keys .= '%' .$uuid; 
                
                $this->cache->save('k5fe3s2ss6cd', $keys, 86400);
                $this->cache->save($uuid, $token, $exp);
            }
        }
    }
?>

