<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CompanySizeRepository")
 */
class CompanySize
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $max_size;

    public function getId()
    {
        return $this->id;
    }

    public function getMaxSize(): ?int
    {
        return $this->max_size;
    }

    public function setMaxSize(int $max_size): self
    {
        $this->max_size = $max_size;

        return $this;
    }
}
