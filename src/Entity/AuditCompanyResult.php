<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AuditCompanyResultRepository")
 */
class AuditCompanyResult
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Company")
     */
    private $company;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AuditTestPhase")
     */
    private $test;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $information;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $selection;


    /**
     * @ORM\Column(type="boolean", nullable=true)
*/
    private $selected;

    

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $done;

    /**
     * @ORM\Column(type="boolean")
     */
    private $passed;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $note;

    /**
     * AuditCompanyResult constructor.
     * @param $company
     * @param $test
     * @param $information
     * @param $selection
     */
    public function __construct($company, $test, $information, $selection, $passed)
    {
        $this->company = $company;
        $this->test = $test;
        $this->information = $information;
        $this->selection = $selection;
        $this->passed = $passed;
    }


    public function getId()
    {
        return $this->id;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

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

    public function getInformation(): ?string
    {
        return $this->information;
    }

    public function setInformation(?string $information): self
    {
        $this->information = $information;

        return $this;
    }

    public function getSelection(): ?string
    {
        return $this->selection;
    }

    public function setSelection(?string $selection): self
    {
        $this->selection = $selection;

        return $this;
    }

    public function getPassed(): ?bool
    {
        return $this->passed;
    }

    public function setPassed(bool $passed): self
    {
        $this->passed = $passed;

        return $this;
    }

    public function getSelected(): ?bool
    {
        return $this->selected;
    }

    public function setSelected(?bool $selected): self
    {
        $this->selected = $selected;

        return $this;
    }

    public function getDone(): ?bool
    {
        return $this->done;
    }

    public function setDone(bool $done): self
    {
        $this->done = $done;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): self
    {
        $this->note = $note;

        return $this;
    }
}
