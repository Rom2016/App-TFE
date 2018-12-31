<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\InfraCustomerRepository")
 */
class InfraCustomer
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AuditTestsInfra", inversedBy="infraCustomers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $infra;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\IntCustomer", inversedBy="infra")
     * @ORM\JoinColumn(nullable=false)
     */
    private $customer;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $result;

    /**
     * InfraCustomer constructor.
     * @param $infra
     * @param $customer
     * @param $result
     */
    public function __construct($infra, $customer, $result)
    {
        $this->infra = $infra;
        $this->customer = $customer;
        $this->result = $result;
    }


    public function getId(): ?int
    {
        return $this->id;
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

    public function getCustomer(): ?IntCustomer
    {
        return $this->customer;
    }

    public function setCustomer(?IntCustomer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function getResult(): ?string
    {
        return $this->result;
    }

    public function setResult(string $result): self
    {
        $this->result = $result;

        return $this;
    }
}
