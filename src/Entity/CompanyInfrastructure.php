<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CompanyInfrastructureRepository")
 */
class CompanyInfrastructure
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Company")
     */
    private $company;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AuditTestInfrastructure")
     */
    private $infra;

    /**
     * CompanyInfrastructure constructor.
     * @param $company
     * @param $infra
     */
    public function __construct($company, $infra)
    {
        $this->company = $company;
        $this->infra = $infra;
    }


    public function getId()
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

    public function getInfra(): ?AuditTestInfrastructure
    {
        return $this->infra;
    }

    public function setInfra(?AuditTestInfrastructure $infra): self
    {
        $this->infra = $infra;

        return $this;
    }
}
