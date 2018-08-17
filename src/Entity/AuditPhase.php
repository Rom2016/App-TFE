<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AuditPhaseRepository")
 */
class AuditPhase
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    public $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    public $phase_name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\AuditTestPhase", mappedBy="idPhase")
     */
    public $test;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    public $number;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $glyphicon;

    /**
     * AuditPhase constructor.
     * @param $phase_name
     */
    public function __construct($phase_name)
    {
        $this->phase_name = $phase_name;
    }


    public function getId()
    {
        return $this->id;
    }

    public function getPhaseName(): ?string
    {
        return $this->phase_name;
    }

    public function setPhaseName(string $phase_name): self
    {
        $this->phase_name = $phase_name;

        return $this;
    }

    /**
     * @return Collection|AuditTestPhase[]
     */
    public function getTest(): Collection
    {
        return $this->test;
    }

    public function addTest(AuditTestPhase $test): self
    {
        if (!$this->test->contains($test)) {
            $this->test[] = $test;
            $test->setIdPhase($this);
        }

        return $this;
    }

    public function removeTest(AuditTestPhase $test): self
    {
        if ($this->test->contains($test)) {
            $this->test->removeElement($test);
            // set the owning side to null (unless already changed)
            if ($test->getIdPhase() === $this) {
                $test->setIdPhase(null);
            }
        }

        return $this;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(?int $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getGlyphicon(): ?string
    {
        return $this->glyphicon;
    }

    public function setGlyphicon(string $glyphicon): self
    {
        $this->glyphicon = $glyphicon;

        return $this;
    }
}
