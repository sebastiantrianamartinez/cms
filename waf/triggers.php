<?php

use Doctrine\ORM\EntityManagerInterface;

class Triggers
{
    protected $entityManager;

    public function __construct($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function logEvent($user, $event, $cause, $description, $data, $service) {
        $logEntry = new Waflogs();
        $logEntry->setIp($_SERVER['REMOTE_ADDR']);
        $logEntry->setAgent($_SERVER['HTTP_USER_AGENT']);
        $logEntry->setUser($user);
        $logEntry->setEvent($event);
        $logEntry->setCause($cause);
        $logEntry->setDescription($description);
        $logEntry->setData($data);
        $logEntry->setService($service);

        $this->entityManager->persist($logEntry);
        $this->entityManager->flush();

        return $token->getTokenId();
    }


}
