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

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $date_creation;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_archive;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\InfraCustomer", mappedBy="infra")
     */
    private $infraCustomers;

    public function __construct()
    {
        $this->linkTestsInfras = new ArrayCollection();
        $this->infraSelections = new ArrayCollection();
        $this->infraCustomers = new ArrayCollection();
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

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->date_creation;
    }

    public function setDateCreation(?\DateTimeInterface $date_creation): self
    {
        $this->date_creation = $date_creation;

        return $this;
    }

    public function getDateArchive(): ?\DateTimeInterface
    {
        return $this->date_archive;
    }

    public function setDateArchive(?\DateTimeInterface $date_archive): self
    {
        $this->date_archive = $date_archive;

        return $this;
    }

    /**
     * @return Collection|InfraCustomer[]
     */
    public function getInfraCustomers(): Collection
    {
        return $this->infraCustomers;
    }

    public function addInfraCustomer(InfraCustomer $infraCustomer): self
    {
        if (!$this->infraCustomers->contains($infraCustomer)) {
            $this->infraCustomers[] = $infraCustomer;
            $infraCustomer->setInfra($this);
        }

        return $this;
    }

    public function removeInfraCustomer(InfraCustomer $infraCustomer): self
    {
        if ($this->infraCustomers->contains($infraCustomer)) {
            $this->infraCustomers->removeElement($infraCustomer);
            // set the owning side to null (unless already changed)
            if ($infraCustomer->getInfra() === $this) {
                $infraCustomer->setInfra(null);
            }
        }

        return $this;
    }
}
