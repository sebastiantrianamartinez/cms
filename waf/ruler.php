<?php
    (!defined('ROOT')) ? define('ROOT', dirname(__FILE__, 2)) : "";
    require_once ROOT .'/core/routing/routing.php';
    
    Routing::vendor();

    use Core\Database\Entities\Rules;
    use Core\Database\Entities\Users;
    use Core\Database\Entities\Services;

    class Ruler{
        private $entityManager;
        private $config;

        public function __construct($entityManager)
        {
            $this->entityManager = $entityManager;
            $this->config = Routing::config('waf');
        }

        public function getMatchs(Users $user = null, Services $service = null) {
            $qb = $this->entityManager->createQueryBuilder();
            $qb->select('r')
                ->from(Rules::class, 'r');
        
            $expr = $qb->expr();
        
            $qb->where(
                $expr->orX(
                    $expr->andX(
                        $expr->eq('r.ip', ':ip'),
                        $expr->isNull('r.agent')
                    ),
                    $expr->eq('r.user', ':user'),
                    $expr->andX(
                        $expr->eq('r.ip', ':ip'),
                        $expr->eq('r.agent', ':agent')
                    )
                ),
                $expr->orX(
                    $expr->gt('r.exp', ':exp'),
                    $expr->isNull('r.exp') 
                ),
                $expr->orX(
                    $expr->isNull('r.service'),
                    $expr->eq('r.service', ':service')
                )
            );
            
            $userId = ($user != null) ? $user->getId() : null;
            $serviceId = ($service != null) ? $service->getId() : null;

            $qb->setParameter('ip', $_SERVER["REMOTE_ADDR"]); 
            $qb->setParameter('user', $userId);
            $qb->setParameter('agent', $_SERVER["HTTP_USER_AGENT"]); 
            $qb->setParameter('exp', new \DateTime(date('Y-m-d H:i:s', time())));
            $qb->setParameter('service', $serviceId);
        
            $result = $qb->getQuery()->getResult();
        
            return $result;
        }

        function create(User $user = null, Services $service = null, int $code, int $cause = null, string $message = null, int $exp = null, bool $setAgent = null) {
            if(is_null($exp)){
                $sanction = $this->config["waf_codes"][$code]["sanction"];     
                $exp = ($sanction < 0) ? null : $exp = $this->config["sanctions"][$sanction][1];
                $exp = ($exp < 0) ? null : $exp;
            }
            $userId = (!is_null($user)) ? $user->getId() : null;
            $serviceId = (!is_null($service)) ? $service->getId() : null;

            $rule = new Rules();
            $rule->setCode($code)
                ->setCause($cause)
                ->setMessage($message)
                ->setUser($userId)
                ->setIp()
                ->setIat()
                ->setExp($exp)
                ->setService($serviceId);

            if(is_null($setAgent) || $setAgent){
                $rule->setAgent();
            } 
        
            $this->entityManager->persist($rule);
            $this->entityManager->flush();
        
            return $rule;
        }
    }
