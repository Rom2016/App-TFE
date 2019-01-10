<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AuditCreatorRepository")
 */
class AuditCreator
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AppUser", inversedBy="creations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $creator;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\IntAudit", inversedBy="creators")
     */
    private $audit;

    /**
     * AuditCreator constructor.
     * @param $creator
     * @param $audit
     */
    public function __construct($creator, $audit)
    {
        $this->creator = $creator;
        $this->audit = $audit;
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
