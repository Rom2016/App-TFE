<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\IntCustomerRepository")
 */
class IntCustomer
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
    private $name;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $email;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\IntAudit", mappedBy="customer")
     */
    private $intAudits;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\InfraCustomer", mappedBy="customer")
     */
    private $infra;

    /**
     * IntCustomer constructor.
     * @param $name
     * @param $email
     */
    public function __construct($name, $email)
    {
        $this->name = $name;
        $this->email = $email;
        $this->infra = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

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
            $intAudit->setCustomer($this);
        }

        return $this;
    }

    public function removeIntAudit(IntAudit $intAudit): self
    {
        if ($this->intAudits->contains($intAudit)) {
            $this->intAudits->removeElement($intAudit);
            // set the owning side to null (unless already changed)
            if ($intAudit->getCustomer() === $this) {
                $intAudit->setCustomer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|InfraCustomer[]
     */
    public function getInfra(): Collection
    {
        return $this->infra;
    }

    public function addInfra(InfraCustomer $infra): self
    {
        if (!$this->infra->contains($infra)) {
            $this->infra[] = $infra;
            $infra->setCustomer($this);
        }

        return $this;
    }

    public function removeInfra(InfraCustomer $infra): self
    {
        if ($this->infra->contains($infra)) {
            $this->infra->removeElement($infra);
            // set the owning side to null (unless already changed)
            if ($infra->getCustomer() === $this) {
                $infra->setCustomer(null);
            }
        }

        return $this;
    }
}
