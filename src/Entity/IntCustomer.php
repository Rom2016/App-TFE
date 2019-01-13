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
    private $customer;

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
     * @ORM\Column(type="string", length=100)
     */
    private $first_name;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $second_name;

    /**
     * IntCustomer constructor.
     * @param $name
     * @param $email
     */
    public function __construct($customer, $first_name, $second_name, $email)
    {
        $this->customer = $customer;
        $this->first_name = $first_name;
        $this->second_name = $second_name;
        $this->email = $email;
        $this->infra = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return mixed
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @param mixed $customer
     */
    public function setCustomer($customer): void
    {
        $this->customer = $customer;
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    /**
     * @param mixed $first_name
     */
    public function setFirstName($first_name): void
    {
        $this->first_name = $first_name;
    }




    public function getSecondName(): ?string
    {
        return $this->second_name;
    }

    public function setSecondName(string $second_name): self
    {
        $this->second_name = $second_name;

        return $this;
    }
}
