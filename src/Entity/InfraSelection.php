<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\InfraSelectionRepository")
 */
class InfraSelection
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
    private $selection;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AuditTestsInfra", inversedBy="infraSelections")
     * @ORM\JoinColumn(nullable=false)
     */
    private $infra;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AuditTests", inversedBy="infraSelections")
     */
    private $test;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $status;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $action;


    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSelection(): ?string
    {
        return $this->selection;
    }

    public function setSelection(string $selection): self
    {
        $this->selection = $selection;

        return $this;
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

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(?bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getAction(): ?bool
    {
        return $this->action;
    }

    public function setAction(?bool $action): self
    {
        $this->action = $action;

        return $this;
    }

}
