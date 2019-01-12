<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TestSelectionsRepository")
 */
class TestSelections
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
    private $selection;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AuditTests", inversedBy="Selections")
     */
    private $test;



    /**
     * @ORM\Column(type="datetime")
     */
    private $date_creation;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_archive;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\InfraSelection", mappedBy="infra")
     */
    private $infraSelections;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Status")
     * @ORM\JoinColumn(nullable=false)
     */
    private $status;



    /**
     * TestSelections constructor.
     * @param $selection
     * @param $test
     * @param $status
     * @param $date_creation
     */
    public function __construct($selection, $test, $status, $date_creation)
    {
        $this->selection = $selection;
        $this->test = $test;
        $this->status = $status;
        $this->date_creation = $date_creation;
        $this->infraSelections = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSelection(): ?string
    {
        return $this->selection;
    }

    public function setSelection(string $selection): self
    {
        $this->selection = $selection;

        return $this;
    }

    public function getTest(): ?audittests
    {
        return $this->test;
    }

    public function setTest(?audittests $test): self
    {
        $this->test = $test;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status): void
    {
        $this->status = $status;
    }




    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->date_creation;
    }

    public function setDateCreation(\DateTimeInterface $date_creation): self
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
