<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AuditSectionRepository")
 */
class AuditSection
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $section;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\AuditSubSection", mappedBy="section")
     */
    private $auditSubSections;

    public function __construct()
    {
        $this->auditSubSections = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSection(): ?string
    {
        return $this->section;
    }

    public function setSection(string $section): self
    {
        $this->section = $section;

        return $this;
    }

    /**
     * @return Collection|AuditSubSection[]
     */
    public function getAuditSubSections(): Collection
    {
        return $this->auditSubSections;
    }

    public function addAuditSubSection(AuditSubSection $auditSubSection): self
    {
        if (!$this->auditSubSections->contains($auditSubSection)) {
            $this->auditSubSections[] = $auditSubSection;
            $auditSubSection->setSection($this);
        }

        return $this;
    }

    public function removeAuditSubSection(AuditSubSection $auditSubSection): self
    {
        if ($this->auditSubSections->contains($auditSubSection)) {
            $this->auditSubSections->removeElement($auditSubSection);
            // set the owning side to null (unless already changed)
            if ($auditSubSection->getSection() === $this) {
                $auditSubSection->setSection(null);
            }
        }

        return $this;
    }
}
