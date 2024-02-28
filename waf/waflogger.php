<?php

    (!defined('ROOT')) ? define('ROOT', dirname(__FILE__, 2)) : "";
    require_once ROOT .'/core/routing/routing.php';
    
    Routing::vendor();

    use Core\Database\Entities\Waflogs;
    use Core\Database\Entities\Users;
    use Core\Database\Entities\Services;

    class Waflogger {
        private $entityManager;
        private $config;

        public function __construct($entityManager) {
            $this->entityManager = $entityManager;
            $this->config = Routing::config('waf');
        }

        public function getMatchs(Users $user = null, Services $service = null, int $code = null, int $effect, bool $filterAgent = null) {
            $qb = $this->entityManager->createQueryBuilder();
            $qb->select('w')
               ->from(Waflogs::class, 'w');
        
            $expr = $qb->expr();
        
            $orX = $expr->orX();
        
            // Check if IP matches and apply the agent condition if $filterAgent is true or null
            $andX = $expr->andX(
                $expr->eq('w.ip', ':ip')
            );
            if ($filterAgent === null || $filterAgent) {
                $andX->add($expr->eq('w.agent', ':agent'));
            }
            $orX->add($andX);
        
            $orX->add($expr->eq('w.user', ':user'));
        
            // Add the condition to filter by service if $service is not null
            if ($service !== null) {
                $orX->add($expr->eq('w.service', ':service'));
            }
        
            $qb->where($orX);
        
            if (!is_null($code)) {
                $qb->andWhere(
                    $expr->eq('w.cause', ':code')
                );
            }
        
            $qb->andWhere(
                $expr->gt('w.iat', ':time')
            );
        
            $userId = ($user !== null) ? $user->getId() : null;
        
            $qb->setParameter('ip', $_SERVER["REMOTE_ADDR"]);
            $qb->setParameter('user', $userId);
            $qb->setParameter('time', new \DateTime(date('Y-m-d H:i:s', time() - $effect)));
            if (!is_null($code)) {
                $qb->setParameter('code', $code);
            }
            if ($service !== null) {
                $qb->setParameter('service', $service->getId());
            }
            if ($filterAgent === null || $filterAgent) {
                $qb->setParameter('agent', $_SERVER["HTTP_USER_AGENT"]);
            }
        
            $result = $qb->getQuery()->getResult();
        
            return [$result, count($result)];
        }
        
        
        public function create(Users $user = null, Services $service = null, $event, $cause = null, $description, $data){
            $userId = (!is_null($user)) ? $user->getId() : null;
            $serviceId = (!is_null($service)) ? $service->getId() : null;

            $logEntry = new Waflogs();
            $logEntry->setIp();
            $logEntry->setAgent();
            $logEntry->setUser($userId);
            $logEntry->setEvent($event);
            $logEntry->setCause($cause);
            $logEntry->setDescription($description);
            $logEntry->setData($data);
            $logEntry->setService(1);//$serviceId);
    
            $this->entityManager->persist($logEntry);
            $this->entityManager->flush();
        }

    }

    /*$waflog = new Waflog();
    echo $waflog->getMatchs(null, null, 3296000)[1];*/