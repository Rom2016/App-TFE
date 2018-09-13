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
     * @ORM\ManyToOne(targetEntity="App\Entity\AuditCompany", inversedBy="auditCompanyResults")
     */
    private $audit;

    /**
     * AuditCompanyResult constructor.
     * @param $test
     * @param $information
     * @param $selection
     * @param $passed
     */
    public function __construct($test, $information, $selection, $passed, $audit)
    {
        $this->test = $test;
        $this->information = $information;
        $this->selection = $selection;
        $this->passed = $passed;
        $this->audit = $audit;

    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getTest()
    {
        return $this->test;
    }

    /**
     * @param mixed $test
     */
    public function setTest($test): void
    {
        $this->test = $test;
    }

    /**
     * @return mixed
     */
    public function getInformation()
    {
        return $this->information;
    }

    /**
     * @param mixed $information
     */
    public function setInformation($information): void
    {
        $this->information = $information;
    }

    /**
     * @return mixed
     */
    public function getSelection()
    {
        return $this->selection;
    }

    /**
     * @param mixed $selection
     */
    public function setSelection($selection): void
    {
        $this->selection = $selection;
    }

    /**
     * @return mixed
     */
    public function getSelected()
    {
        return $this->selected;
    }

    /**
     * @param mixed $selected
     */
    public function setSelected($selected): void
    {
        $this->selected = $selected;
    }

    /**
     * @return mixed
     */
    public function getDone()
    {
        return $this->done;
    }

    /**
     * @param mixed $done
     */
    public function setDone($done): void
    {
        $this->done = $done;
    }

    /**
     * @return mixed
     */
    public function getPassed()
    {
        return $this->passed;
    }

    /**
     * @param mixed $passed
     */
    public function setPassed($passed): void
    {
        $this->passed = $passed;
    }

    /**
     * @return mixed
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @param mixed $note
     */
    public function setNote($note): void
    {
        $this->note = $note;
    }

    /**
     * @return mixed
     */
    public function getAudit()
    {
        return $this->audit;
    }

    /**
     * @param mixed $audit
     */
    public function setAudit($audit): void
    {
        $this->audit = $audit;
    }



}
