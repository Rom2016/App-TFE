<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserPermissionRepository")
 */
class UserPermission
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AppUser", inversedBy="auditPermission")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AuditPermission")
     * @ORM\JoinColumn(nullable=false)
     */
    private $permission;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\IntAudit")
     * @ORM\JoinColumn(nullable=false)
     */
    private $audit;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?AppUser
    {
        return $this->user;
    }

    public function setUser(?AppUser $user): self
    {
        $this->user = $user;

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

    public function getAudit(): ?IntAudit
    {
        return $this->audit;
    }

    public function setAudit(?IntAudit $audit): self
    {
        $this->audit = $audit;

        return $this;
    }
}
