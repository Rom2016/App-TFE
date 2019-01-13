<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LogAdminCustomerRepository")
 */
class LogAdminCustomer
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AppUser")
     * @ORM\JoinColumn(nullable=false)
     */
    private $creator;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\IntCustomer")
     * @ORM\JoinColumn(nullable=false)
     */
    private $customer;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_creation;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreator(): ?AppUser
    {
        return $this->creator;
    }

    public function setCreator(?AppUser $creator): self
    {
        $this->creator = $creator;

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
