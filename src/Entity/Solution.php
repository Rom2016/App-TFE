<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SolutionRepository")
 */
class Solution
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=200)
     */
    private $solution;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AuditTests", inversedBy="solutions")
     * @ORM\JoinColumn(nullable=false)
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
     * @ORM\OneToMany(targetEntity="App\Entity\AuditResults", mappedBy="solution")
     */
    private $auditResults;

    /**
     * Solution constructor.
     * @param $solution
     * @param $test
     * @param $date_creation
     */
    public function __construct($solution, $test, $date_creation)
    {
        $this->solution = $solution;
        $this->test = $test;
        $this->date_creation = $date_creation;
        $this->auditResults = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSolution(): ?string
    {
        return $this->solution;
    }

    public function setSolution(string $solution): self
    {
        $this->solution = $solution;

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
     * @return Collection|AuditResults[]
     */
    public function getAuditResults(): Collection
    {
        return $this->auditResults;
    }

    public function addAuditResult(AuditResults $auditResult): self
    {
        if (!$this->auditResults->contains($auditResult)) {
            $this->auditResults[] = $auditResult;
            $auditResult->setSolution($this);
        }

        return $this;
    }

    public function removeAuditResult(AuditResults $auditResult): self
    {
        if ($this->auditResults->contains($auditResult)) {
            $this->auditResults->removeElement($auditResult);
            // set the owning side to null (unless already changed)
            if ($auditResult->getSolution() === $this) {
                $auditResult->setSolution(null);
            }
        }

        return $this;
    }
}
