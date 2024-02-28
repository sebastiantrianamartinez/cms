<?php
namespace Core\Database\Entities;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="waflogs")
 */
class Waflogs
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="waflog_id", type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="waflog_ip", type="string", length=45)
     */
    private $ip;

    /**
     * @ORM\Column(name="waflog_agent", type="string", length=255)
     */
    private $agent;

    /**
     * @ORM\Column(name="waflog_user", type="integer", nullable=true)
     */
    private $user;

    /**
     * @ORM\Column(name="waflog_event", type="integer")
     */
    private $event;

    /**
     * @ORM\Column(name="waflog_cause", type="integer", nullable=true)
     */
    private $cause;

    /**
     * @ORM\Column(name="waflog_description", type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(name="waflog_data", type="text", nullable=true)
     */
    private $data;

    /**
     * @ORM\Column(name="waflog_service", type="integer")
     */
    private $service;

    /**
     * @ORM\Column(name="waflog_iat", type="datetime")
     */
    private $iat;

    // Getters and setters...

    public function getId(): ?int
    {
        return $this->id;
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

    public function getUser(): ?int
    {
        return $this->user;
    }

    public function setUser(?int $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getEvent(): ?int
    {
        return $this->event;
    }

    public function setEvent(int $event): self
    {
        $this->event = $event;

        return $this;
    }

    public function getCause(): ?int
    {
        return $this->cause;
    }

    public function setCause(int $cause = null): self
    {
        $this->cause = $cause;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getData(): ?string
    {
        return $this->data;
    }

    public function setData(?string $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getService(): ?int
    {
        return $this->service;
    }

    public function setService(int $service = null): self
    {
        $this->service = $service;

        return $this;
    }

    public function getIat(): ?\DateTimeInterface
    {
        return $this->iat;
    }

    public function setIat(): self
    {
        $timestamp = time();
        $this->iat = (new \DateTime())->setTimestamp($timestamp);

        return $this;
    }
}
