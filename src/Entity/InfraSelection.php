<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\InfraSelectionRepository")
 */
class InfraSelection
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
    private $selection;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AuditTestsInfra", inversedBy="infraSelections")
     * @ORM\JoinColumn(nullable=false)
     */
    private $infra;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_creation;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_archive;
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\LinkSelectInfra", mappedBy="selection")
     */
    private $link;

    public function __construct()
    {
        $this->link = new ArrayCollection();
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

    public function getInfra(): ?AuditTestsInfra
    {
        return $this->infra;
    }

    public function setInfra(?AuditTestsInfra $infra): self
    {
        $this->infra = $infra;

        return $this;
    }

    public function getTest(): ?AuditTests
    {
        return $this->test;
    }

    public function setTest(?AuditTests $test): self
    {
        $this->test = $test;

        return $this;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(?bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getAction(): ?bool
    {
        return $this->action;
    }

    public function setAction(?bool $action): self
    {
        $this->action = $action;

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

    public function setDateArchive(\DateTimeInterface $date_archive): self
    {
        $this->date_archive = $date_archive;

        return $this;
    }

    /**
     * @return Collection|LinkSelectInfra[]
     */
    public function getLink(): Collection
    {
        return $this->link;
    }

    public function addLink(LinkSelectInfra $link): self
    {
        if (!$this->link->contains($link)) {
            $this->link[] = $link;
            $link->setSelection($this);
        }

        return $this;
    }

    public function removeLink(LinkSelectInfra $link): self
    {
        if ($this->link->contains($link)) {
            $this->link->removeElement($link);
            // set the owning side to null (unless already changed)
            if ($link->getSelection() === $this) {
                $link->setSelection(null);
            }
        }

        return $this;
    }

}
