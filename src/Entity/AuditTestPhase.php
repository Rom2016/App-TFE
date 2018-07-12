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
    public $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    public $name;

    /**
     * @ORM\Column(type="integer")
     */
    public $priority;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AuditPhase", inversedBy="test")
     * @ORM\JoinColumn(nullable=false)
     */
    public $idPhase;

    /**
     * AuditTestPhase constructor.
     * @param $name
     * @param $priority
     * @param $idPhase
     */
    public function __construct($name, $priority, $idPhase)
    {
        $this->name = $name;
        $this->priority = $priority;
        $this->idPhase = $idPhase;
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
