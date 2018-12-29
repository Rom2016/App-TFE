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


}
