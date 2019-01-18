<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LinkSnapPreRepository")
 */
class LinkSnapPre
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Snapshot", inversedBy="pre_audit")
     * @ORM\JoinColumn(nullable=false)
     */
    private $snap;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AuditTestsInfra")
     * @ORM\JoinColumn(nullable=false)
     */
    private $pre;

    /**
     * LinkSnapPre constructor.
     * @param $snap
     * @param $pre
     */
    public function __construct($snap, $pre)
    {
        $this->snap = $snap;
        $this->pre = $pre;
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

    public function getPreAudit(): ?AuditTestsInfra
    {
        return $this->pre_audit;
    }

    public function setPreAudit(?AuditTestsInfra $pre_audit): self
    {
        $this->pre_audit = $pre_audit;

        return $this;
    }

    public function getPre(): ?AuditTestsInfra
    {
        return $this->pre;
    }

    public function setPre(?AuditTestsInfra $pre): self
    {
        $this->pre = $pre;

        return $this;
    }
}
