<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User
{
    function __construct($prenom,$nom,$mdp,$email)
    {
        $this->prenom = $prenom;
        $this->nom = $nom;
        $this->mdp = $mdp;
        $this->email = $email;
    }

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
     * @ORM\Column(type="string", length=100)
     */
    public $pass;

    /**
     * @ORM\Column(type="string", length=50, unique=true)
     */
    public $email;


    public function startConnection(){
        session_start();
        $_SESSION['user'] = $this;
    }

    public function logoff(){
        session_destroy();
    }

    public function checkPassword($pass,$salt){
        $password=md5($pass.$salt);
        if($this->pass == $password){
            return true;
        }else{
            return false;
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getMdp(): ?string
    {
        return $this->mdp;
    }

    public function setMdp(string $mdp): self
    {
        $this->mdp = $mdp;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }
}
