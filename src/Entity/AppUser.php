<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AppUserRepository")
 */
class AppUser implements UserInterface, \Serializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    public $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    public $username;

    /**
     * @ORM\Column(type="string", length=100)
     */
    public $password;

    /**
     * @ORM\Column(type="string", length=50)
     */
    public $first_name;

    /**
     * @ORM\Column(type="string", length=50)
     */
    public $second_name;

    /**
     * @ORM\Column(type="string", length=25)
     */
    public $function;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_creation;


    /**
     * @ORM\OneToMany(targetEntity="App\Entity\AuditCompany", mappedBy="owner")
     */
    private $audit;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Roles", inversedBy="appUsers")
     */
    private $role;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    public $profile_pic;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AppUser")
     */
    private $creator;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastModifiedDate;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AppUser")
     */
    private $lastModifiedBy;

    /**
     * AppUser constructor.
     * @param $username
     * @param $password
     * @param $first_name
     * @param $second_name
     * @param $function
     * @param $date_creation
     * @param $role
     * @param $creator
     */
    public function __construct($username, $password, $first_name, $second_name, $function, $date_creation, $role, $creator)
    {
        $this->username = $username;
        $this->password = $password;
        $this->first_name = $first_name;
        $this->second_name = $second_name;
        $this->function = $function;
        $this->date_creation = $date_creation;
        $this->role = $role;
        $this->creator = $creator;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(string $first_name): self
    {
        $this->first_name = $first_name;

        return $this;
    }

    public function getSecondName(): ?string
    {
        return $this->second_name;
    }

    public function setSecondName(string $second_name): self
    {
        $this->second_name = $second_name;

        return $this;
    }

    public function getFunction(): ?string
    {
        return $this->function;
    }

    public function setFunction(string $function): self
    {
        $this->function = $function;

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

    public function getRoles()
    {
            $roles[] =$this->getRole()->getRole();
        return $roles;
    }

    public function eraseCredentials()
    {
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            $this->first_name,
            $this->second_name,
            $this->function,
            $this->date_creation,
            $this->profile_pic

            // see section on salt below
            // $this->salt,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            $this->first_name,
            $this->second_name,
            $this->function,
            $this->date_creation,
            $this->profile_pic
            // see section on salt below
            // $this->salt
            ) = unserialize($serialized, array('allowed_classes' => false));
    }

    public function getSalt()
    {
        // you *may* need a real salt depending on your encoder
        // see section on salt below
        return null;
    }

   

    /**
     * @return Collection|AuditCompany[]
     */
    public function getAudit(): Collection
    {
        return $this->audit;
    }

    public function addAudit(AuditCompany $audit): self
    {
        if (!$this->audit->contains($audit)) {
            $this->audit[] = $audit;
            $audit->setUser($this);
        }

        return $this;
    }

    public function removeAudit(AuditCompany $audit): self
    {
        if ($this->audit->contains($audit)) {
            $this->audit->removeElement($audit);
            // set the owning side to null (unless already changed)
            if ($audit->getUser() === $this) {
                $audit->setUser(null);
            }
        }

        return $this;
    }

    public function getRole(): ?Roles
    {
        return $this->role;
    }

    public function setRole(?Roles $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getProfilePic(): ?string
    {
        return $this->profile_pic;
    }

    public function setProfilePic(?string $profile_pic): self
    {
        $this->profile_pic = $profile_pic;

        return $this;
    }

    public function getCreator(): ?self
    {
        return $this->creator;
    }

    public function setCreator(?self $creator): self
    {
        $this->creator = $creator;

        return $this;
    }

    public function getLastModifiedDate(): ?\DateTimeInterface
    {
        return $this->lastModifiedDate;
    }

    public function setLastModifiedDate(?\DateTimeInterface $lastModifiedDate): self
    {
        $this->lastModifiedDate = $lastModifiedDate;

        return $this;
    }

    public function getLastModifiedBy(): ?self
    {
        return $this->lastModifiedBy;
    }

    public function setLastModifiedBy(?self $lastModifiedBy): self
    {
        $this->lastModifiedBy = $lastModifiedBy;

        return $this;
    }

  
}
