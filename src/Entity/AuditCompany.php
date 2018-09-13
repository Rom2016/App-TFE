<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AuditCompanyRepository")
 */
class AuditCompany
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Company", inversedBy="audit")
     */
    public $company;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\AuditCompanyResult", mappedBy="audit")
     */
    private $auditCompanyResults;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AppUser", inversedBy="audit")
     */
    private $owner;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_creation;

    /**
     * AuditCompany constructor.
     * @param $company
     * @param $owner
     * @param $date_creation
     */
    public function __construct($company, $owner, $date_creation)
    {
        $this->company = $company;
        $this->owner = $owner;
        $this->date_creation = $date_creation;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

        return $this;
    }

    /**
     * @return Collection|AuditCompanyResult[]
     */
    public function getAuditCompanyResults(): Collection
    {
        return $this->auditCompanyResults;
    }

    public function addAuditCompanyResult(AuditCompanyResult $auditCompanyResult): self
    {
        if (!$this->auditCompanyResults->contains($auditCompanyResult)) {
            $this->auditCompanyResults[] = $auditCompanyResult;
            $auditCompanyResult->setAudit($this);
        }

        return $this;
    }

    public function removeAuditCompanyResult(AuditCompanyResult $auditCompanyResult): self
    {
        if ($this->auditCompanyResults->contains($auditCompanyResult)) {
            $this->auditCompanyResults->removeElement($auditCompanyResult);
            // set the owning side to null (unless already changed)
            if ($auditCompanyResult->getAudit() === $this) {
                $auditCompanyResult->setAudit(null);
            }
        }

        return $this;
    }

    public function getOwner(): ?AppUser
    {
        return $this->owner;
    }

    public function setOwner(?AppUser $owner): self
    {
        $this->owner = $owner;

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
}
