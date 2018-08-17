<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User
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
    public $first_name;

    /**
     * @ORM\Column(type="string", length=50)
     */
    public $second_name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    public $pass;

    /**
     * @ORM\Column(type="string", length=50, unique=true)
     */
    public $email;

    /**
     * @ORM\Column(type="string", length=100, nullable=true )
     */
    public $profile_pic;

    /**
     * @ORM\Column(type="string", length=50)
     */
    public $function;

    /**
     * @ORM\Column(type="string", length=50)
     */
    public $phone;

    /**
     * @ORM\Column(type="boolean")
     */
    public $isAdmin;

    /**
     * User constructor.
     * @param $first_name
     * @param $second_name
     * @param $email
     * @param $function
     * @param $phone
     * @param $isAdmin
     */
    public function __construct($first_name, $second_name, $email, $function, $phone, $isAdmin)
    {
        $this->first_name = $first_name;
        $this->second_name = $second_name;
        $this->email = $email;
        $this->function = $function;
        $this->phone = $phone;
        $this->isAdmin = $isAdmin;
    }


    public function startConnection(){
        session_start();
        $_SESSION['user'] = $this;
    }

    public function selectProfilePic(){
        if($this->profile_pic){
            return $this->profile_pic;
        }else{
            return 'default_profile.png';
        }
    }

    public function generatePassword($length) {

        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        return substr(str_shuffle($chars),0,$length);
    }


    public function updateProfile(){
        $this->setFirstName($_POST['fName']);
        $this->setSecondName($_POST['sName']);
        $this->setEmail($_POST['email']);
        $this->setFunction($_POST['function']);
        $this->setPhone($_POST['phone']);

        $_SESSION['user'] = $this;
    }

    public function getAll(){
        $array=['id'=>$this->getId(),'fName'=>$this->first_name,'sName'=>$this->second_name,'email'=>$this->email,'profPic'=>$this->selectProfilePic(),'phone'=>$this->phone,'function'=>$this->function,'is_admin'=>$this->isAdmin];
        return $array;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    /**
     * @param mixed $first_name
     */
    public function setFirstName($first_name): void
    {
        $this->first_name = $first_name;
    }

    /**
     * @return mixed
     */
    public function getSecondName()
    {
        return $this->second_name;
    }

    /**
     * @param mixed $second_name
     */
    public function setSecondName($second_name): void
    {
        $this->second_name = $second_name;
    }

    /**
     * @return mixed
     */
    public function getPass()
    {
        return $this->pass;
    }

    /**
     * @param mixed $pass
     */
    public function setPass($pass): void
    {
        $this->pass = $pass;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email): void
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getProfilePic()
    {
        return $this->profile_pic;
    }

    /**
     * @param mixed $profile_pic
     */
    public function setProfilePic($profile_pic): void
    {
        $this->profile_pic = $profile_pic;
    }

    /**
     * @return mixed
     */
    public function getFunction()
    {
        return $this->function;
    }

    /**
     * @param mixed $function
     */
    public function setFunction($function): void
    {
        $this->function = $function;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param mixed $phone
     */
    public function setPhone($phone): void
    {
        $this->phone = $phone;
    }

    public function getSalt(): ?string
    {
        return $this->salt;
    }

    public function setSalt(string $salt): self
    {
        $this->salt = $salt;

        return $this;
    }

    public function getIsAdmin(): ?bool
    {
        return $this->isAdmin;
    }

    public function setIsAdmin(bool $isAdmin): self
    {
        $this->isAdmin = $isAdmin;

        return $this;
    }




}
