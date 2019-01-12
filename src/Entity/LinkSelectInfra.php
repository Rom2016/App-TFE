<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LinkSelectInfraRepository")
 */
class LinkSelectInfra
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\InfraSelection", inversedBy="link")
     * @ORM\JoinColumn(nullable=false)
     */
    private $selection;


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
     * @ORM\ManyToOne(targetEntity="App\Entity\AuditTests", inversedBy="linkSelectInfras")
     * @ORM\JoinColumn(nullable=false)
     */
    private $test;

    /**
     * LinkSelectInfra constructor.
     * @param $selection
     * @param $action
     * @param $date_creation
     * @param $test
     */
    public function __construct($selection, $action, $date_creation, $test)
    {
        $this->selection = $selection;
        $this->action = $action;
        $this->date_creation = $date_creation;
        $this->test = $test;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSelection(): ?InfraSelection
    {
        return $this->selection;
    }

    public function setSelection(?InfraSelection $selection): self
    {
        $this->selection = $selection;

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
