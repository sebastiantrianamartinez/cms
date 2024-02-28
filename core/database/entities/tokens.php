<?php
    namespace Core\Database\Entities;

    use Doctrine\ORM\Mapping as ORM;

    /**
     * @ORM\Entity
     * @ORM\Table(name="tokens")
     */
    class Tokens
    {
        /**
         * @ORM\Id
         * @ORM\GeneratedValue(strategy="AUTO")
         * @ORM\Column(name="token_id", type="integer")
         */
        private $id;

        /**
         * @ORM\Column(name="token_value", type="string")
         */
        private $value;

        /**
         * @ORM\Column(name="token_iat", type="datetime")
         */
        private $iat;

        /**
         * @ORM\Column(name="token_exp", type="datetime")
         */
        private $exp;

        /**
         * @ORM\Column(name="token_user", type="integer")
         */
        private $user;

        /**
         * @ORM\Column(name="token_ip", type="string", length=64)
         */
        private $ip;

        /**
         * @ORM\Column(name="token_agent", type="string")
         */
        private $agent;

        /**
         * @ORM\Column(name="token_ping", type="datetime")
         */
        private $ping;

        // Getters and setters...

        public function getId(): ?int
        {
            return $this->id;
        }

        public function getValue(): ?string
        {
            return $this->value;
        }

        public function setValue(string $value): self
        {
            $this->value = $value;

            return $this;
        }

        public function getIat(): ?\DateTimeInterface
        {
            return $this->iat;
        }

        public function setIat(): self
        {
            $this->iat = new \DateTime();

            return $this;
        }

        public function getExp(): ?\DateTimeInterface
        {
            return $this->exp;
        }

        public function setExp(int $exp): self
        {
            $timestamp = time() + $exp;
            $this->exp = (new \DateTime())->setTimestamp($timestamp);

            return $this;
        }

        public function getUser(): ?int
        {
            return $this->user;
        }

        public function setUser(int $user): self
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

        public function getPing(): ?\DateTimeInterface
        {
            return $this->ping;
        }

        public function setPing(): self
        {
            $this->ping = new \DateTime();

            return $this;
        }
    }
