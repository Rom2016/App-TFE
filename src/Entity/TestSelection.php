<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TestSelectionRepository")
 */
class TestSelection
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
     * @ORM\ManyToOne(targetEntity="App\Entity\AuditTestPhase")
     */
    public $test;

    /**
     * TestSelection constructor.
     * @param $name
     * @param $test
     */
    public function __construct($name, $test)
    {
        $this->name = $name;
        $this->test = $test;
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

    public function getTest(): ?AuditTestPhase
    {
        return $this->test;
    }

    public function setTest(?AuditTestPhase $test): self
    {
        $this->test = $test;

        return $this;
    }
}
