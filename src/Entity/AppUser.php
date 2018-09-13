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
     * @ORM\OneToMany(targetEntity="App\Entity\UserRole", mappedBy="user")
     */
    private $userRoles;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\AuditCompany", mappedBy="owner")
     */
    private $audit;

    /**
     * AppUser constructor.
     * @param $username
     * @param $password
     * @param $first_name
     * @param $second_name
     * @param $function
     * @param $date_creation
     */
    public function __construct($username, $password, $first_name, $second_name, $function, $date_creation)
    {
        $this->username = $username;
        $this->password = $password;
        $this->first_name = $first_name;
        $this->second_name = $second_name;
        $this->function = $function;
        $this->date_creation = $date_creation;
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
        foreach ($this->getUserRoles() as $key => $value){
            $roles[] = $value->getRole()->getRole();
        }
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
            $this->date_creation
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
            $this->date_creation
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
     * @return Collection|UserRole[]
     */
    public function getUserRoles(): Collection
    {
        return $this->userRoles;
    }

    public function addUserRole(UserRole $userRole): self
    {
        if (!$this->userRoles->contains($userRole)) {
            $this->userRoles[] = $userRole;
            $userRole->setUser($this);
        }

        return $this;
    }

    public function removeUserRole(UserRole $userRole): self
    {
        if ($this->userRoles->contains($userRole)) {
            $this->userRoles->removeElement($userRole);
            // set the owning side to null (unless already changed)
            if ($userRole->getUser() === $this) {
                $userRole->setUser(null);
            }
        }

        return $this;
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

  
}
