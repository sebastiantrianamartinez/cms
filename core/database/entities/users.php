<?php

namespace Core\Database\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class Users
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="user_id", type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="user_name", type="string", length=30)
     */
    private $name;

    /**
     * @ORM\Column(name="user_mail", type="string", length=255)
     */
    private $mail;

    /**
     * @ORM\Column(name="user_password", type="string")
     */
    private $password;

    /**
     * @ORM\Column(name="user_group", type="integer")
     */
    private $group;

    /**
     * @ORM\Column(name="user_alias", type="string", length=500)
     */
    private $alias;

    /**
     * @ORM\Column(name="user_status", type="integer", options={"default" : 1})
     */
    private $status;

    // Getters and setters...

    public function setId(int $id = null): self
    {
        $this->id = $id;
        
        return $this;
    }

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

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): self
    {
        $this->mail = $mail;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = password_hash($password, PASSWORD_BCRYPT);

        return $this;
    }

    public function getGroup(): ?int
    {
        return $this->group;
    }

    public function setGroup(int $group): self
    {
        $this->group = $group;

        return $this;
    }

    public function getAlias(): ?string
    {
        return $this->alias;
    }

    public function setAlias(string $alias): self
    {
        $this->alias = $alias;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status = null): self
    {
        $status = (is_null($status)) ? 1 : $status; 
        $this->status = $status;
        return $this;
    }
}
