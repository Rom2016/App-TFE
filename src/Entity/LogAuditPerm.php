<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LogAuditPermRepository")
 */
class LogAuditPerm
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
    private $source;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AppUser")
     * @ORM\JoinColumn(nullable=false)
     */
    private $recipient;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\IntAudit", inversedBy="logPerm")
     * @ORM\JoinColumn(nullable=false)
     */
    private $audit;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AuditPermission")
     */
    private $permission;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * LogAuditPerm constructor.
     * @param $source
     * @param $recipient
     * @param $audit
     * @param $permission
     * @param $date
     */
    public function __construct($source, $recipient, $audit, $permission, $date)
    {
        $this->source = $source;
        $this->recipient = $recipient;
        $this->audit = $audit;
        $this->permission = $permission;
        $this->date = $date;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSource(): ?AppUser
    {
        return $this->source;
    }

    public function setSource(?AppUser $source): self
    {
        $this->source = $source;

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

    public function getAudit(): ?IntAudit
    {
        return $this->audit;
    }

    public function setAudit(?IntAudit $audit): self
    {
        $this->audit = $audit;

        return $this;
    }

    public function getPermission(): ?AuditPermission
    {
        return $this->permission;
    }

    public function setPermission(?AuditPermission $permission): self
    {
        $this->permission = $permission;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }
}
