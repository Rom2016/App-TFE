<?php

namespace App\Entity;

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
    public $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    public $name;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    public $cost;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\CompanySize")
     * @ORM\JoinColumn(nullable=false)
     */
    public $id_size;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AuditTestPhase")
     */
    public $id_test;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $pic;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

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

    public function getCost(): ?string
    {
        return $this->cost;
    }

    public function setCost(?string $cost): self
    {
        $this->cost = $cost;

        return $this;
    }

    public function getIdSize(): ?CompanySize
    {
        return $this->id_size;
    }

    public function setIdSize(?CompanySize $id_size): self
    {
        $this->id_size = $id_size;

        return $this;
    }

    public function getIdTest(): ?AuditTestPhase
    {
        return $this->id_test;
    }

    public function setIdTest(?AuditTestPhase $id_test): self
    {
        $this->id_test = $id_test;

        return $this;
    }

    public function getPic(): ?string
    {
        return $this->pic;
    }

    public function setPic(?string $pic): self
    {
        $this->pic = $pic;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }
}
