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
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    public $profile_pic;



    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserNotifications", mappedBy="receiver")
     */
    private $Notifications;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserRoles", mappedBy="user")
     */
    private $userRoles;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserPermission", mappedBy="user")
     */
    private $auditPermission;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\AuditCreator", mappedBy="creator")
     */
    private $creations;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $deactivated;

    /**
     * AppUser constructor.
     * @param $username
     * @param $password
     * @param $first_name
     * @param $second_name
     * @param $function
     * @param $date_creation
     * @param $creator
     */
    public function __construct($username, $password, $first_name, $second_name, $function, $date_creation)
    {
        $this->username = $username;
        $this->password = $password;
        $this->first_name = $first_name;
        $this->second_name = $second_name;
        $this->function = $function;
        $this->date_creation = $date_creation;
        //$this->logs = new ArrayCollection();
        $this->intAuditPermissions = new ArrayCollection();
        $this->intAudits = new ArrayCollection();
        $this->Notifications = new ArrayCollection();
        $this->userRoles = new ArrayCollection();
        $this->auditPermission = new ArrayCollection();
        $this->creations = new ArrayCollection();
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






    /**
     * @return Collection|IntAudit[]
     */
    public function getIntAudits(): Collection
    {
        return $this->intAudits;
    }

    public function addIntAudit(IntAudit $intAudit): self
    {
        if (!$this->intAudits->contains($intAudit)) {
            $this->intAudits[] = $intAudit;
            $intAudit->setCreator($this);
        }

        return $this;
    }

    public function removeIntAudit(IntAudit $intAudit): self
    {
        if ($this->intAudits->contains($intAudit)) {
            $this->intAudits->removeElement($intAudit);
            // set the owning side to null (unless already changed)
            if ($intAudit->getCreator() === $this) {
                $intAudit->setCreator(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|UserNotifications[]
     */
    public function getNotifications(): Collection
    {
        return $this->Notifications;
    }

    public function addNotification(UserNotifications $notification): self
    {
        if (!$this->Notifications->contains($notification)) {
            $this->Notifications[] = $notification;
            $notification->setReceiver($this);
        }

        return $this;
    }

    public function removeNotification(UserNotifications $notification): self
    {
        if ($this->Notifications->contains($notification)) {
            $this->Notifications->removeElement($notification);
            // set the owning side to null (unless already changed)
            if ($notification->getReceiver() === $this) {
                $notification->setReceiver(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|UserRoles[]
     */
    public function getUserRoles(): Collection
    {
        return $this->userRoles;
    }

    public function addUserRole(UserRoles $userRole): self
    {
        if (!$this->userRoles->contains($userRole)) {
            $this->userRoles[] = $userRole;
            $userRole->setUser($this);
        }

        return $this;
    }

    public function removeUserRole(UserRoles $userRole): self
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
    public function getRoles()
    {
        $role = $this->getUserRoles();
        foreach ($role as $key => $value){
            $roles[] =$value->getRole()->getRole();
        }
        return $roles;
    }

    /**
     * @return Collection|UserPermission[]
     */
    public function getAuditPermission(): Collection
    {
        return $this->auditPermission;
    }

    public function addAuditPermission(UserPermission $auditPermission): self
    {
        if (!$this->auditPermission->contains($auditPermission)) {
            $this->auditPermission[] = $auditPermission;
            $auditPermission->setUser($this);
        }

        return $this;
    }

    public function removeAuditPermission(UserPermission $auditPermission): self
    {
        if ($this->auditPermission->contains($auditPermission)) {
            $this->auditPermission->removeElement($auditPermission);
            // set the owning side to null (unless already changed)
            if ($auditPermission->getUser() === $this) {
                $auditPermission->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|AuditCreator[]
     */
    public function getCreations(): Collection
    {
        return $this->creations;
    }

    public function addCreation(AuditCreator $creation): self
    {
        if (!$this->creations->contains($creation)) {
            $this->creations[] = $creation;
            $creation->setCreator($this);
        }

        return $this;
    }

    public function removeCreation(AuditCreator $creation): self
    {
        if ($this->creations->contains($creation)) {
            $this->creations->removeElement($creation);
            // set the owning side to null (unless already changed)
            if ($creation->getCreator() === $this) {
                $creation->setCreator(null);
            }
        }

        return $this;
    }

    public function getDeactivated(): ?bool
    {
        return $this->deactivated;
    }

    public function setDeactivated(?bool $deactivated): self
    {
        $this->deactivated = $deactivated;

        return $this;
    }


  
}
