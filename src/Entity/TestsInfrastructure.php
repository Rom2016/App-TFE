<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TestsInfrastructureRepository")
 */
class TestsInfrastructure
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AuditTestInfrastructure")
     */
    public $test_infra;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AuditTestPhase")
     */
    public $test_phase;

    public function getId()
    {
        return $this->id;
    }

    public function getTestInfra(): ?AuditTestInfrastructure
    {
        return $this->test_infra;
    }

    public function setTestInfra(?AuditTestInfrastructure $test_infra): self
    {
        $this->test_infra = $test_infra;

        return $this;
    }

    public function getTestPhase(): ?AuditTestPhase
    {
        return $this->test_phase;
    }

    public function setTestPhase(?AuditTestPhase $test_phase): self
    {
        $this->test_phase = $test_phase;

        return $this;
    }
}
