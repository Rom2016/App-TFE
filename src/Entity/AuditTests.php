<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AuditTestsRepository")
 */
class AuditTests
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
    private $test;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $priority;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AuditSubSection", inversedBy="auditTests")
     */
    private $susbection;



    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TestType", inversedBy="auditTests")
     */
    private $type;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_creation;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_archive;



    /**
     * @ORM\OneToMany(targetEntity="App\Entity\TestSelections", mappedBy="test")
     */
    private $Selections;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AuditTests")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\LinkTestsInfra", mappedBy="test")
     */
    private $linkTestsInfras;


    /**
     * @ORM\OneToMany(targetEntity="App\Entity\AuditResults", mappedBy="test")
     */
    private $auditResults;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Solution", mappedBy="test")
     */
    private $solutions;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\LinkSelectInfra", mappedBy="test")
     */
    private $linkSelectInfras;

    /**
     * AuditTests constructor.
     * @param $test
     * @param $priority
     * @param $susbection
     * @param $type
     * @param $date_creation
     */
    public function __construct($test, $priority, $susbection, $type, $date_creation)
    {
        $this->test = $test;
        $this->priority = $priority;
        $this->susbection = $susbection;
        $this->type = $type;
        $this->date_creation = $date_creation;
        $this->testSelections = new ArrayCollection();
        $this->Selections = new ArrayCollection();
        $this->linkTestsInfras = new ArrayCollection();
        $this->auditResults = new ArrayCollection();
        $this->solutions = new ArrayCollection();
        $this->linkSelectInfras = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTest(): ?string
    {
        return $this->test;
    }

    public function setTest(string $test): self
    {
        $this->test = $test;

        return $this;
    }

    public function getPriority(): ?string
    {
        return $this->priority;
    }

    public function setPriority(string $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

    public function getSusbection(): ?AuditSubSection
    {
        return $this->susbection;
    }

    public function setSusbection(?AuditSubSection $susbection): self
    {
        $this->susbection = $susbection;

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    public function getType(): ?TestType
    {
        return $this->type;
    }

    public function setType(?TestType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->date_creation;
    }

    public function setDateCreation(\DateTimeInterface $date_creation): self
    {
        $this->date_creation = $date_creation;

        return $this;
    }

    public function getDateArchive(): ?\DateTimeInterface
    {
        return $this->date_archive;
    }

    public function setDateArchive(?\DateTimeInterface $date_archive): self
    {
        $this->date_archive = $date_archive;

        return $this;
    }

    /**
     * @return Collection|TestSelection[]
     */
    public function getTestSelections(): Collection
    {
        return $this->testSelections;
    }



    /**
     * @return Collection|TestSelections[]
     */
    public function getSelections(): Collection
    {
        return $this->Selections;
    }

    public function addSelection(TestSelections $selection): self
    {
        if (!$this->Selections->contains($selection)) {
            $this->Selections[] = $selection;
            $selection->setTest($this);
        }

        return $this;
    }

    public function removeSelection(TestSelections $selection): self
    {
        if ($this->Selections->contains($selection)) {
            $this->Selections->removeElement($selection);
            // set the owning side to null (unless already changed)
            if ($selection->getTest() === $this) {
                $selection->setTest(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|LinkTestsInfra[]
     */
    public function getLinkTestsInfras(): Collection
    {
        return $this->linkTestsInfras;
    }

    public function addLinkTestsInfra(LinkTestsInfra $linkTestsInfra): self
    {
        if (!$this->linkTestsInfras->contains($linkTestsInfra)) {
            $this->linkTestsInfras[] = $linkTestsInfra;
            $linkTestsInfra->setTest($this);
        }

        return $this;
    }

    public function removeLinkTestsInfra(LinkTestsInfra $linkTestsInfra): self
    {
        if ($this->linkTestsInfras->contains($linkTestsInfra)) {
            $this->linkTestsInfras->removeElement($linkTestsInfra);
            // set the owning side to null (unless already changed)
            if ($linkTestsInfra->getTest() === $this) {
                $linkTestsInfra->setTest(null);
            }
        }

        return $this;
    }




    /**
     * @return Collection|AuditResults[]
     */
    public function getAuditResults(): Collection
    {
        return $this->auditResults;
    }

    public function addAuditResult(AuditResults $auditResult): self
    {
        if (!$this->auditResults->contains($auditResult)) {
            $this->auditResults[] = $auditResult;
            $auditResult->setTest($this);
        }

        return $this;
    }

    public function removeAuditResult(AuditResults $auditResult): self
    {
        if ($this->auditResults->contains($auditResult)) {
            $this->auditResults->removeElement($auditResult);
            // set the owning side to null (unless already changed)
            if ($auditResult->getTest() === $this) {
                $auditResult->setTest(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Solution[]
     */
    public function getSolutions(): Collection
    {
        return $this->solutions;
    }

    public function addSolution(Solution $solution): self
    {
        if (!$this->solutions->contains($solution)) {
            $this->solutions[] = $solution;
            $solution->setTest($this);
        }

        return $this;
    }

    public function removeSolution(Solution $solution): self
    {
        if ($this->solutions->contains($solution)) {
            $this->solutions->removeElement($solution);
            // set the owning side to null (unless already changed)
            if ($solution->getTest() === $this) {
                $solution->setTest(null);
            }
        }

        return $this;
    }





    /**
     * @return Collection|LinkSelectInfra[]
     */
    public function getLinkSelectInfras(): Collection
    {
        return $this->linkSelectInfras;
    }

    public function addLinkSelectInfra(LinkSelectInfra $linkSelectInfra): self
    {
        if (!$this->linkSelectInfras->contains($linkSelectInfra)) {
            $this->linkSelectInfras[] = $linkSelectInfra;
            $linkSelectInfra->setTest($this);
        }

        return $this;
    }

    public function removeLinkSelectInfra(LinkSelectInfra $linkSelectInfra): self
    {
        if ($this->linkSelectInfras->contains($linkSelectInfra)) {
            $this->linkSelectInfras->removeElement($linkSelectInfra);
            // set the owning side to null (unless already changed)
            if ($linkSelectInfra->getTest() === $this) {
                $linkSelectInfra->setTest(null);
            }
        }

        return $this;
    }


}
