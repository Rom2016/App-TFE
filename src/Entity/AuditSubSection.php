<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AuditSubSectionRepository")
 */
class AuditSubSection
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $subsection;
    


    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AuditSection", inversedBy="auditSubSections")
     */
    private $section;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\AuditTests", mappedBy="susbection")
     */
    private $auditTests;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_creation;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_archive;

    /**
     * AuditSubSection constructor.
     * @param $subsection
     * @param $section
     * @param $date_creation
     */
    public function __construct($subsection, $section, $date_creation)
    {
        $this->subsection = $subsection;
        $this->section = $section;
        $this->date_creation = $date_creation;
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
    public function getSubsection()
    {
        return $this->subsection;
    }

    /**
     * @param mixed $subsection
     */
    public function setSubsection($subsection): void
    {
        $this->subsection = $subsection;
    }

    /**
     * @return mixed
     */
    public function getSection()
    {
        return $this->section;
    }

    /**
     * @param mixed $section
     */
    public function setSection($section): void
    {
        $this->section = $section;
    }

    /**
     * @return mixed
     */
    public function getAuditTests()
    {
        return $this->auditTests;
    }

    /**
     * @param mixed $auditTests
     */
    public function setAuditTests($auditTests): void
    {
        $this->auditTests = $auditTests;
    }

    /**
     * @return mixed
     */
    public function getDateCreation()
    {
        return $this->date_creation;
    }

    /**
     * @param mixed $date_creation
     */
    public function setDateCreation($date_creation): void
    {
        $this->date_creation = $date_creation;
    }

    /**
     * @return mixed
     */
    public function getDateArchive()
    {
        return $this->date_archive;
    }

    /**
     * @param mixed $date_archive
     */
    public function setDateArchive($date_archive): void
    {
        $this->date_archive = $date_archive;
    }



}
