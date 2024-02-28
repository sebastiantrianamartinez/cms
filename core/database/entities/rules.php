<?php

namespace Core\Database\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="rules")
 */
class Rules
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="rule_id", type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="rule_code", type="integer")
     */
    private $code;

    /**
     * @ORM\Column(name="rule_cause", type="integer", nullable=true)
     */
    private $cause;

    /**
     * @ORM\Column(name="rule_message", type="string", nullable=true)
     */
    private $message;

    /**
     * @ORM\Column(name="rule_user", type="integer", nullable=true)
     */
    private $user;

    /**
     * @ORM\Column(name="rule_ip", type="string", length=54, nullable=true)
     */
    private $ip;

    /**
     * @ORM\Column(name="rule_agent", type="string", nullable=true)
     */
    private $agent;

    /**
     * @ORM\Column(name="rule_iat", type="datetime")
     */
    private $iat;

    /**
     * @ORM\Column(name="rule_exp", type="datetime", nullable=true)
     */
    private $exp;

    /**
     * @ORM\Column(name="rule_service", type="integer", nullable=true)
     */
    private $service;

    // Getters and setters...

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?int
    {
        return $this->code;
    }

    public function setCode(int $code): self
    {
        $this->code = 800 + $code;

        return $this;
    }

    public function getCause(): ?int
    {
        return $this->cause;
    }

    public function setCause(?int $cause): self
    {
        $this->cause = $cause;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getUser(): ?int
    {
        return $this->user;
    }

    public function setUser(?int $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(): self
    {
        $this->ip = $_SERVER["REMOTE_ADDR"];

        return $this;
    }

    public function getAgent(): ?string
    {
        return $this->agent;
    }

    public function setAgent(): self
    {
        $this->agent = $_SERVER["HTTP_USER_AGENT"];

        return $this;
    }

    public function getIat(): ?\DateTime
    {
        return $this->iat;
    }

    public function setIat(): self
    {
        $timestamp = time();
        $this->iat = (new \DateTime())->setTimestamp($timestamp);
        

        return $this;
    }

    public function getExp(): ?\DateTime
    {
        return $this->exp;
        
    }

    public function setExp(?int $exp): self
    {
        $timestamp = (!is_null($exp)) ? time() + $exp : null;
        $this->exp = (!is_null($exp)) ? (new \DateTime())->setTimestamp($timestamp) : null;

        return $this;
    }

    public function getService(): ?int
    {
        return $this->service;
    }

    public function setService(?int $service): self
    {
        $this->service = $service;

        return $this;
    }
}
