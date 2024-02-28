<?php
namespace Core\Database\Entities;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="services")
 */
class Services {
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="service_id", type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="service_name", type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(name="service_description", type="string")
     */
    private $description;

    /**
     * @ORM\Column(name="service_status", type="integer")
     */
    private $status;

    /**
     * @ORM\Column(name="service_exp", type="integer", nullable=true)
     */
    private $exp;

    /**
     * @ORM\Column(name="service_timeout", type="integer", nullable=true)
     */
    private $timeout;

    /**
     * @ORM\Column(name="service_level", type="integer", nullable=true)
     */
    private $level;

    /**
     * @ORM\Column(name="service_key", type="string", length=60)
     */
    private $key;

    // Getters and setters...

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getExp(): ?int
    {
        return $this->exp;
    }

    public function setExp(?int $exp): self
    {
        $this->exp = $exp;

        return $this;
    }

    public function getTimeout(): ?int
    {
        return $this->timeout;
    }

    public function setTimeout(?int $timeout): self
    {
        $this->timeout = $timeout;

        return $this;
    }

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(?int $level): self
    {
        $this->level = $level;

        return $this;
    }

    public function getKey(): ?string
    {
        return $this->key;
    }

    public function setKey(string $key): self
    {
        $this->key = $key;

        return $this;
    }
}
