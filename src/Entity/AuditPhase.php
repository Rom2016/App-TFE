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
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $phase_name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\AuditTestPhase", mappedBy="idPhase")
     */
    private $test;

    public function __construct()
    {
        $this->test = new ArrayCollection();
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
}
