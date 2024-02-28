<?php
namespace Core\Database\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="activation")
 */
class Activation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="activation_id", type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="activation_user", type="integer")
     */
    private $userId;


    /**
     * @ORM\Column(name="activation_token", type="text", length=255)
     */
    private $token;

    /**
     * @ORM\Column(name="activation_code", type="integer", length=8)
     */
    private $code;

    /**
     * @ORM\Column(name="activation_time", type="datetime")
     */
    private $time;

    // Getters and setters...

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getCode(): ?int
    {
        return $this->code;
    }

    public function setCode(int $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getTime(): ?int
    {
        return $this->time;
    }

    public function setTime(int $time): self
    {
        $dateTime = new \DateTime();
        $dateTime::createFromFormat('Y-m-d H:i:s', $time);
        $this->time = $dateTime;
        return $this;
    }
}
