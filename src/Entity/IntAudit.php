<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\IntAuditRepository")
 */
class IntAudit
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;


    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\IntCustomer", inversedBy="intAudits")
     */
    private $customer;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_creation;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $started;

 
    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $finish_date;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\AuditResults", mappedBy="audit")
     */
    private $auditResults;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_archive;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $confidential;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $last_activity;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\AuditCreator", mappedBy="audit")
     */
    private $creators;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\InfraCustomer", mappedBy="audit")
     */
    private $infraCustomers;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\IntAudit")
     */
    private $parent;



    /**
     * IntAudit constructor.
     * @param $customer
     * @param $date_creation
     * @param $name
     * @param $auditResults
     * @param $creators
     */
    public function __construct($customer, $date_creation, $name)
    {
        $this->customer = $customer;
        $this->date_creation = $date_creation;
        $this->name = $name;
        $this->infraCustomers = new ArrayCollection();
        $this->intAudits = new ArrayCollection();
        $this->audits = new ArrayCollection();

    }


    public function getId(): ?int
    {
        return $this->id;
    }



    public function getCustomer(): ?IntCustomer
    {
        return $this->customer;
    }

    public function setCustomer(?IntCustomer $customer): self
    {
        $this->customer = $customer;

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


    /**
     * @return mixed
     */
    public function getStarted()
    {
        return $this->started;
    }

    /**
     * @param mixed $started
     */
    public function setStarted($started): void
    {
        $this->started = $started;
    }



    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFinishDate()
    {
        return $this->finish_date;
    }

    /**
     * @param mixed $finish_date
     */
    public function setFinishDate($finish_date): void
    {
        $this->finish_date = $finish_date;
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
            $auditResult->setAudit($this);
        }

        return $this;
    }

    public function removeAuditResult(AuditResults $auditResult): self
    {
        if ($this->auditResults->contains($auditResult)) {
            $this->auditResults->removeElement($auditResult);
            // set the owning side to null (unless already changed)
            if ($auditResult->getAudit() === $this) {
                $auditResult->setAudit(null);
            }
        }

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

    public function getConfidential(): ?bool
    {
        return $this->confidential;
    }

    public function setConfidential(?bool $confidential): self
    {
        $this->confidential = $confidential;

        return $this;
    }

    public function getLastActivity(): ?\DateTimeInterface
    {
        return $this->last_activity;
    }

    public function setLastActivity(?\DateTimeInterface $last_activity): self
    {
        $this->last_activity = $last_activity;

        return $this;
    }

    /**
     * @return Collection|AuditCreator[]
     */
    public function getCreators(): Collection
    {
        return $this->creators;
    }

    public function addCreator(AuditCreator $creator): self
    {
        if (!$this->creators->contains($creator)) {
            $this->creators[] = $creator;
            $creator->setAudit($this);
        }

        return $this;
    }

    public function removeCreator(AuditCreator $creator): self
    {
        if ($this->creators->contains($creator)) {
            $this->creators->removeElement($creator);
            // set the owning side to null (unless already changed)
            if ($creator->getAudit() === $this) {
                $creator->setAudit(null);
            }
        }

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
            $infraCustomer->setAudit($this);
        }

        return $this;
    }

    public function removeInfraCustomer(InfraCustomer $infraCustomer): self
    {
        if ($this->infraCustomers->contains($infraCustomer)) {
            $this->infraCustomers->removeElement($infraCustomer);
            // set the owning side to null (unless already changed)
            if ($infraCustomer->getAudit() === $this) {
                $infraCustomer->setAudit(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|IntAudit[]
     */
    public function getIntAudits(): Collection
    {
        return $this->intAudits;
    }

    public function addIntAudit(IntAudit $intAudit): self
    {
        if (!$this->intAudits->contains($intAudit)) {
            $this->intAudits[] = $intAudit;
            $intAudit->setParent($this);
        }

        return $this;
    }

    public function removeIntAudit(IntAudit $intAudit): self
    {
        if ($this->intAudits->contains($intAudit)) {
            $this->intAudits->removeElement($intAudit);
            // set the owning side to null (unless already changed)
            if ($intAudit->getParent() === $this) {
                $intAudit->setParent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|IntAudit[]
     */
    public function getAudits(): Collection
    {
        return $this->audits;
    }

    public function addAudit(IntAudit $audit): self
    {
        if (!$this->audits->contains($audit)) {
            $this->audits[] = $audit;
            $audit->setParent($this);
        }

        return $this;
    }

    public function removeAudit(IntAudit $audit): self
    {
        if ($this->audits->contains($audit)) {
            $this->audits->removeElement($audit);
            // set the owning side to null (unless already changed)
            if ($audit->getParent() === $this) {
                $audit->setParent(null);
            }
        }

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }


}
