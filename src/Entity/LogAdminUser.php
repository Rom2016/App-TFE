<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LogAdminUserRepository")
 */
class LogAdminUser
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AppUser")
     * @ORM\JoinColumn(nullable=false)
     */
    private $creator;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AppUser")
     * @ORM\JoinColumn(nullable=false)
     */
    private $recipient;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\LogAction")
     * @ORM\JoinColumn(nullable=false)
     */
    private $action;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_creation;

    /**
     * LogAdminUser constructor.
     * @param $creator
     * @param $recipient
     * @param $action
     * @param $date_creation
     */
    public function __construct($creator, $recipient, $action, $date_creation)
    {
        $this->creator = $creator;
        $this->recipient = $recipient;
        $this->action = $action;
        $this->date_creation = $date_creation;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreator(): ?AppUser
    {
        return $this->creator;
    }

    public function setCreator(?AppUser $creator): self
    {
        $this->creator = $creator;

        return $this;
    }

    public function getRecipient(): ?AppUser
    {
        return $this->recipient;
    }

    public function setRecipient(?AppUser $recipient): self
    {
        $this->recipient = $recipient;

        return $this;
    }

    public function getAction(): ?LogAction
    {
        return $this->action;
    }

    public function setAction(?LogAction $action): self
    {
        $this->action = $action;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->date_creation;
    }

    public function setDateCreation(\DateTimeInterface $date_creation): self
    {
        $this->date_creation = $date_creation;

        return $this;
    }
}
