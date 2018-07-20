<?php

namespace App\Entity;

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
     * @ORM\ManyToOne(targetEntity="App\Entity\Company", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $idCompany;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AuditTestPhase")
     * @ORM\JoinColumn(nullable=false)
     */
    private $idTest;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $information;

    /**
     * @ORM\Column(type="boolean")
     */
    private $selected;

    /**
     * AuditCompany constructor.
     * @param $idCompany
     * @param $idTest
     */
    public function __construct($idCompany, $idTest)
    {
        $this->idCompany = $idCompany;
        $this->idTest = $idTest;
    }


    public function getId()
    {
        return $this->id;
    }

    public function getIdCompany(): ?Company
    {
        return $this->idCompany;
    }

    public function setIdCompany(Company $idCompany): self
    {
        $this->idCompany = $idCompany;

        return $this;
    }

    public function getIdTest(): ?AuditTestPhase
    {
        return $this->idTest;
    }

    public function setIdTest(?AuditTestPhase $idTest): self
    {
        $this->idTest = $idTest;

        return $this;
    }

    public function getInformation(): ?string
    {
        return $this->information;
    }

    public function setInformation(?string $information): self
    {
        $this->information = $information;

        return $this;
    }

    public function getSelected(): ?bool
    {
        return $this->selected;
    }

    public function setSelected(bool $selected): self
    {
        $this->selected = $selected;

        return $this;
    }
}
