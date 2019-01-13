<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AuditResultsRepository")
 */
class AuditResults
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\IntAudit", inversedBy="auditResults")
     * @ORM\JoinColumn(nullable=false)
     */
    private $audit;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AuditTests", inversedBy="auditResults")
     * @ORM\JoinColumn(nullable=false)
     */
    private $test;



    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Status")
     */
    private $status;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $last_responded;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TestSelections")
     */
    private $selection;


    /**
     * AuditResults constructor.
     * @param $audit
     * @param $test
     */
    public function __construct($audit, $test, $status)
    {
        $this->audit = $audit;
        $this->test = $test;
        $this->status = $status;

    }


    public function getId(): ?int
    {
        return $this->id;
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

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

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

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param mixed $result
     */
    public function setResult($result): void
    {
        $this->result = $result;
    }



    public function getDateResponse(): ?\DateTimeInterface
    {
        return $this->date_response;
    }

    public function setDateResponse(?\DateTimeInterface $date_response): self
    {
        $this->date_response = $date_response;

        return $this;
    }

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function setStatus(?Status $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getLastResponded(): ?bool
    {
        return $this->last_responded;
    }

    public function setLastResponded(?bool $last_responded): self
    {
        $this->last_responded = $last_responded;

        return $this;
    }

    public function getSelection(): ?TestSelections
    {
        return $this->selection;
    }

    public function setSelection(?TestSelections $selection): self
    {
        $this->selection = $selection;

        return $this;
    }
}
