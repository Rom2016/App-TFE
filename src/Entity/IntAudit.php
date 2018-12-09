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
     * @ORM\ManyToOne(targetEntity="App\Entity\AppUser", inversedBy="intAudits")
     */
    private $creator;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\IntCustomer", inversedBy="intAudits")
     */
    private $customer;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_creation;

  

    /**
     * @ORM\Column(type="boolean")
     */
    private $started;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\IntAudit", mappedBy="parent")
     */
    private $parent;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $finish_date;

    /**
     * IntAudit constructor.
     * @param $creator
     * @param $customer
     * @param $date_creation
     * @param $paused
     * @param $name
     */
    public function __construct($creator, $customer, $date_creation, $started, $name)
    {
        $this->creator = $creator;
        $this->customer = $customer;
        $this->date_creation = $date_creation;
        $this->started = $started;
        $this->name = $name;
    }


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

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

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



    public function addParent(IntAudit $parent): self
    {
        if (!$this->parent->contains($parent)) {
            $this->parent[] = $parent;
            $parent->setParent($this);
        }

        return $this;
    }

    public function removeParent(IntAudit $parent): self
    {
        if ($this->parent->contains($parent)) {
            $this->parent->removeElement($parent);
            // set the owning side to null (unless already changed)
            if ($parent->getParent() === $this) {
                $parent->setParent(null);
            }
        }

        return $this;
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


}
