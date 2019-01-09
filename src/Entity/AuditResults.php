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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $result;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_response;

    /**
     * AuditResults constructor.
     * @param $audit
     * @param $test
     */
    public function __construct($audit, $test)
    {
        $this->audit = $audit;
        $this->test = $test;
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

    public function getResult(): ?string
    {
        return $this->result;
    }

    public function setResult(string $result): self
    {
        $this->result = $result;

        return $this;
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
}
