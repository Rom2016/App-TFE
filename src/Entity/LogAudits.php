<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LogAuditsRepository")
 */
class LogAudits
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AppUser")
     * @ORM\JoinColumn(nullable=false)
     */
    private $source;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\IntAudit", inversedBy="logAudits")
     * @ORM\JoinColumn(nullable=false)
     */
    private $audit;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\LogAction")
     * @ORM\JoinColumn(nullable=false)
     */
    private $action;

    /**
     * LogAudits constructor.
     * @param $source
     * @param $audit
     * @param $date
     * @param $action
     */
    public function __construct($source, $audit, $date, $action)
    {
        $this->source = $source;
        $this->audit = $audit;
        $this->date = $date;
        $this->action = $action;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSource(): ?AppUser
    {
        return $this->source;
    }

    public function setSource(?AppUser $source): self
    {
        $this->source = $source;

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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getAction(): ?LogAction
    {
        return $this->action;
    }

    public function setAction(?LogAction $action): self
    {
        $this->action = $action;

        return $this;
    }
}
