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
}
