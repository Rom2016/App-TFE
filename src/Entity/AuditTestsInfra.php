<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AuditTestsInfraRepository")
 */
class AuditTestsInfra
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
    private $test;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TestType", inversedBy="auditTestsInfras")
     * @ORM\JoinColumn(nullable=false)
     */
    private $type;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\LinkTestsInfra", mappedBy="infra")
     */
    private $linkTestsInfras;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\InfraSelection", mappedBy="infra")
     */
    private $infraSelections;

    public function __construct()
    {
        $this->linkTestsInfras = new ArrayCollection();
        $this->infraSelections = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTest(): ?string
    {
        return $this->test;
    }

    public function setTest(string $test): self
    {
        $this->test = $test;

        return $this;
    }

    public function getType(): ?TestType
    {
        return $this->type;
    }

    public function setType(?TestType $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection|LinkTestsInfra[]
     */
    public function getLinkTestsInfras(): Collection
    {
        return $this->linkTestsInfras;
    }

    public function addLinkTestsInfra(LinkTestsInfra $linkTestsInfra): self
    {
        if (!$this->linkTestsInfras->contains($linkTestsInfra)) {
            $this->linkTestsInfras[] = $linkTestsInfra;
            $linkTestsInfra->setInfra($this);
        }

        return $this;
    }

    public function removeLinkTestsInfra(LinkTestsInfra $linkTestsInfra): self
    {
        if ($this->linkTestsInfras->contains($linkTestsInfra)) {
            $this->linkTestsInfras->removeElement($linkTestsInfra);
            // set the owning side to null (unless already changed)
            if ($linkTestsInfra->getInfra() === $this) {
                $linkTestsInfra->setInfra(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|InfraSelection[]
     */
    public function getInfraSelections(): Collection
    {
        return $this->infraSelections;
    }

    public function addInfraSelection(InfraSelection $infraSelection): self
    {
        if (!$this->infraSelections->contains($infraSelection)) {
            $this->infraSelections[] = $infraSelection;
            $infraSelection->setInfra($this);
        }

        return $this;
    }

    public function removeInfraSelection(InfraSelection $infraSelection): self
    {
        if ($this->infraSelections->contains($infraSelection)) {
            $this->infraSelections->removeElement($infraSelection);
            // set the owning side to null (unless already changed)
            if ($infraSelection->getInfra() === $this) {
                $infraSelection->setInfra(null);
            }
        }

        return $this;
    }
}
