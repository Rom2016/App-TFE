<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LinkTestsInfraRepository")
 */
class LinkTestsInfra
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AuditTestsInfra", inversedBy="linkTestsInfras")
     * @ORM\JoinColumn(nullable=false)
     */
    private $infra;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AuditTests", inversedBy="linkTestsInfras")
     * @ORM\JoinColumn(nullable=false)
     */
    private $test;

    /**
     * @ORM\Column(type="boolean")
     */
    private $action;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_creation;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_archive;

    /**
     * LinkTestsInfra constructor.
     * @param $infra
     * @param $test
     * @param $action
     * @param $date_creation
     */
    public function __construct($infra, $test, $action, $date_creation)
    {
        $this->infra = $infra;
        $this->test = $test;
        $this->action = $action;
        $this->date_creation = $date_creation;
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

    public function getTest(): ?AuditTests
    {
        return $this->test;
    }

    public function setTest(?AuditTests $test): self
    {
        $this->test = $test;

        return $this;
    }

    public function getAction(): ?bool
    {
        return $this->action;
    }

    public function setAction(bool $action): self
    {
        $this->action = $action;

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

    public function getDateArchive(): ?\DateTimeInterface
    {
        return $this->date_archive;
    }

    public function setDateArchive(?\DateTimeInterface $date_archive): self
    {
        $this->date_archive = $date_archive;

        return $this;
    }
}
