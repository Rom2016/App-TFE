<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SnapshotRepository")
 */
class Snapshot
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\LinkSnapTest", mappedBy="snap")
     */
    private $test;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_creation;


    public function __construct($name, $date_creation)
    {
        $this->name = $name;
        $this->date_creation = $date_creation;
        $this->test = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|LinkSnapTest[]
     */
    public function getTest(): Collection
    {
        return $this->test;
    }

    public function addTest(LinkSnapTest $test): self
    {
        if (!$this->test->contains($test)) {
            $this->test[] = $test;
            $test->setSnap($this);
        }

        return $this;
    }

    public function removeTest(LinkSnapTest $test): self
    {
        if ($this->test->contains($test)) {
            $this->test->removeElement($test);
            // set the owning side to null (unless already changed)
            if ($test->getSnap() === $this) {
                $test->setSnap(null);
            }
        }

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
}
