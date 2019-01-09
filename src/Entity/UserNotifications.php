<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserNotificationsRepository")
 */
class UserNotifications
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
    private $sender;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AppUser", inversedBy="Notifications")
     * @ORM\JoinColumn(nullable=false)
     */
    private $receiver;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_creation;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSender(): ?AppUser
    {
        return $this->sender;
    }

    public function setSender(?AppUser $sender): self
    {
        $this->sender = $sender;

        return $this;
    }

    public function getReceiver(): ?AppUser
    {
        return $this->receiver;
    }

    public function setReceiver(?AppUser $receiver): self
    {
        $this->receiver = $receiver;

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
