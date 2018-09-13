<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CompanyRepository")
 */
class Company
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    public $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $email;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\CompanySize")
     * @ORM\JoinColumn(nullable=false)
     */
    private $size;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\AuditCompany", mappedBy="company")
     */
    private $audit;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_creation;

    /**
     * Company constructor.
     * @param $name
     * @param $phone
     * @param $email
     */
    public function __construct($name, $phone, $email, $size, $date)
    {
        $this->name = $name;
        $this->phone = $phone;
        $this->email = $email;
        $this->size = $size;
        $this->date_creation = $date;
    }


    public function getId()
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

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param mixed $phone
     */
    public function setPhone($phone): void
    {
        $this->phone = $phone;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email): void
    {
        $this->email = $email;
    }

    public function getSize(): ?CompanySize
    {
        return $this->size;
    }

    public function setSize(?CompanySize $size): self
    {
        $this->size = $size;

        return $this;
    }

    /**
     * @return Collection|AuditCompany[]
     */
    public function getAudit(): Collection
    {
        return $this->audit;
    }

    public function addAudit(AuditCompany $audit): self
    {
        if (!$this->audit->contains($audit)) {
            $this->audit[] = $audit;
            $audit->setCompany($this);
        }

        return $this;
    }

    public function removeAudit(AuditCompany $audit): self
    {
        if ($this->audit->contains($audit)) {
            $this->audit->removeElement($audit);
            // set the owning side to null (unless already changed)
            if ($audit->getCompany() === $this) {
                $audit->setCompany(null);
            }
        }

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
