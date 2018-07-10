<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AuditTestPhaseRepository")
 */
class AuditTestPhase
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
    private $name;

    /**
     * @ORM\Column(type="integer")
     */
    private $priority;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AuditPhase", inversedBy="test")
     * @ORM\JoinColumn(nullable=false)
     */
    private $idPhase;

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

    public function getPriority(): ?int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

    public function getIdPhase(): ?AuditPhase
    {
        return $this->idPhase;
    }

    public function setIdPhase(?AuditPhase $idPhase): self
    {
        $this->idPhase = $idPhase;

        return $this;
    }
}
