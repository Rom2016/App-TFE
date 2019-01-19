<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SectionPointsRepository")
 */
class SectionPoints
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AuditSection")
     * @ORM\JoinColumn(nullable=false)
     */
    private $section;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\IntAudit")
     * @ORM\JoinColumn(nullable=false)
     */
    private $audit;

    /**
     * @ORM\Column(type="integer")
     */
    private $point;

    /**
     * SectionPoints constructor.
     * @param $section
     * @param $audit
     * @param $point
     */
    public function __construct($section, $audit, $point)
    {
        $this->section = $section;
        $this->audit = $audit;
        $this->point = $point;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSection(): ?AuditSection
    {
        return $this->section;
    }

    public function setSection(?AuditSection $section): self
    {
        $this->section = $section;

        return $this;
    }

    public function getAudit(): ?IntAudit
    {
        return $this->audit;
    }

    public function setAudit(?IntAudit $audit): self
    {
        $this->audit = $audit;

        return $this;
    }

    public function getPoint(): ?int
    {
        return $this->point;
    }

    public function setPoint(int $point): self
    {
        $this->point = $point;

        return $this;
    }
}
