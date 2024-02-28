<?php
namespace Core\Database\Entities;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="permissions")
 */
class Permissions
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="permission_id", type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="permission_service", type="integer")
     */
    private $service;

    /**
     * @ORM\Column(name="permission_group", type="integer", nullable=true)
     */
    private $group;

    /**
     * @ORM\Column(name="permission_user", type="integer", nullable=true)
     */
    private $user;

    /**
     * @ORM\Column(name="permission_ip", type="string", length=64, nullable=true)
     */
    private $ip;

    /**
     * @ORM\Column(name="permission_crud", type="integer", options={"default": 15})
     */
    private $crud;

    // Getters and setters...

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getService(): ?int
    {
        return $this->service;
    }

    public function setService(int $service): self
    {
        $this->service = $service;

        return $this;
    }

    public function getGroup(): ?int
    {
        return $this->group;
    }

    public function setGroup(?int $group): self
    {
        $this->group = $group;

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

    public function setIp(?string $ip): self
    {
        $this->ip = $ip;

        return $this;
    }

    public function getCrud(): ?int
    {
        return $this->crud;
    }

    public function setCrud(string $crud): self
    {
        $this->crud = bindec($crud);

        return $this;
    }
}