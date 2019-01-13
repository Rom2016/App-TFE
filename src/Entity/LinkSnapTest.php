<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LinkSnapTestRepository")
 */
class LinkSnapTest
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Snapshot", inversedBy="test")
     * @ORM\JoinColumn(nullable=false)
     */
    private $snap;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AuditTests")
     * @ORM\JoinColumn(nullable=false)
     */
    private $test;

    /**
     * LinkSnapTest constructor.
     * @param $snap
     * @param $test
     */
    public function __construct($snap, $test)
    {
        $this->snap = $snap;
        $this->test = $test;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSnap(): ?Snapshot
    {
        return $this->snap;
    }

    public function setSnap(?Snapshot $snap): self
    {
        $this->snap = $snap;

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
}
