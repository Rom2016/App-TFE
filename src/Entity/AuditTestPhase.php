<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;

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
     * @ORM\Column(type="string", length=255)
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
     * @ORM\ManyToOne(targetEntity="App\Entity\TestType")
     */
    public $type;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AuditTestPhase")
     */
    public $id_parent;

    /**
     * AuditTestPhase constructor.
     * @param $name
     * @param $priority
     * @param $idPhase
     * @param $type
     * @param $id_parent
     */
    public function __construct($name, $priority, $idPhase, $type, $id_parent)
    {
        $this->name = $name;
        $this->priority = $priority;
        $this->idPhase = $idPhase;
        $this->type = $type;
        $this->id_parent = $id_parent;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @param mixed $priority
     */
    public function setPriority($priority): void
    {
        $this->priority = $priority;
    }

    /**
     * @return mixed
     */
    public function getIdPhase()
    {
        return $this->idPhase;
    }

    /**
     * @param mixed $idPhase
     */
    public function setIdPhase($idPhase): void
    {
        $this->idPhase = $idPhase;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type): void
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getIdParent()
    {
        return $this->id_parent;
    }

    /**
     * @param mixed $id_parent
     */
    public function setIdParent($id_parent): void
    {
        $this->id_parent = $id_parent;
    }




}
