<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LogAdminContentRepository")
 */
class LogAdminContent
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
    private $creator;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\LogAction")
     * @ORM\JoinColumn(nullable=false)
     */
    private $action;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $element;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_creation;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * LogAdminContent constructor.
     * @param $creator
     * @param $action
     * @param $element
     * @param $date_creation
     * @param $name
     */
    public function __construct($creator, $action, $element, $date_creation, $name)
    {
        $this->creator = $creator;
        $this->action = $action;
        $this->element = $element;
        $this->date_creation = $date_creation;
        $this->name = $name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreator(): ?AppUser
    {
        return $this->creator;
    }

    public function setCreator(?AppUser $creator): self
    {
        $this->creator = $creator;

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

    public function getElement(): ?string
    {
        return $this->element;
    }

    public function setElement(string $element): self
    {
        $this->element = $element;

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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
