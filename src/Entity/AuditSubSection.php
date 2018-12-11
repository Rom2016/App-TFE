<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AuditSubSectionRepository")
 */
class AuditSubSection
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $subsection;
    

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\AuditTestPhase", mappedBy="subsection")
     */
    private $auditTestPhases;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AuditSection", inversedBy="auditSubSections")
     */
    private $section;

    public function __construct()
    {
        $this->auditTestPhases = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubsection(): ?string
    {
        return $this->subsection;
    }

    public function setSubsection(string $subsection): self
    {
        $this->subsection = $subsection;

        return $this;
    }

    public function getSection(): ?AuditPhase
    {
        return $this->section;
    }

    public function setSection(?AuditPhase $section): self
    {
        $this->section = $section;

        return $this;
    }

    /**
     * @return Collection|AuditTestPhase[]
     */
    public function getAuditTestPhases(): Collection
    {
        return $this->auditTestPhases;
    }

    public function addAuditTestPhase(AuditTestPhase $auditTestPhase): self
    {
        if (!$this->auditTestPhases->contains($auditTestPhase)) {
            $this->auditTestPhases[] = $auditTestPhase;
            $auditTestPhase->setSubsection($this);
        }

        return $this;
    }

    public function removeAuditTestPhase(AuditTestPhase $auditTestPhase): self
    {
        if ($this->auditTestPhases->contains($auditTestPhase)) {
            $this->auditTestPhases->removeElement($auditTestPhase);
            // set the owning side to null (unless already changed)
            if ($auditTestPhase->getSubsection() === $this) {
                $auditTestPhase->setSubsection(null);
            }
        }

        return $this;
    }
}
