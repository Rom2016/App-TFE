<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TestTypeRepository")
 */
class TestType
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
    public $type;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\AuditTests", mappedBy="type")
     */
    private $auditTests;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\AuditTestsInfra", mappedBy="type")
     */
    private $auditTestsInfras;

    public function __construct()
    {
        $this->auditTests = new ArrayCollection();
        $this->auditTestsInfras = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection|AuditTests[]
     */
    public function getAuditTests(): Collection
    {
        return $this->auditTests;
    }

    public function addAuditTest(AuditTests $auditTest): self
    {
        if (!$this->auditTests->contains($auditTest)) {
            $this->auditTests[] = $auditTest;
            $auditTest->setType($this);
        }

        return $this;
    }

    public function removeAuditTest(AuditTests $auditTest): self
    {
        if ($this->auditTests->contains($auditTest)) {
            $this->auditTests->removeElement($auditTest);
            // set the owning side to null (unless already changed)
            if ($auditTest->getType() === $this) {
                $auditTest->setType(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|AuditTestsInfra[]
     */
    public function getAuditTestsInfras(): Collection
    {
        return $this->auditTestsInfras;
    }

    public function addAuditTestsInfra(AuditTestsInfra $auditTestsInfra): self
    {
        if (!$this->auditTestsInfras->contains($auditTestsInfra)) {
            $this->auditTestsInfras[] = $auditTestsInfra;
            $auditTestsInfra->setType($this);
        }

        return $this;
    }

    public function removeAuditTestsInfra(AuditTestsInfra $auditTestsInfra): self
    {
        if ($this->auditTestsInfras->contains($auditTestsInfra)) {
            $this->auditTestsInfras->removeElement($auditTestsInfra);
            // set the owning side to null (unless already changed)
            if ($auditTestsInfra->getType() === $this) {
                $auditTestsInfra->setType(null);
            }
        }

        return $this;
    }
}
