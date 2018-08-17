<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductCompanySizeRepository")
 */
class ProductCompanySize
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Product")
     */
    private $product;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\CompanySize")
     */
    private $size;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\AuditTestPhase")
     */
    private $test;

    public function getId()
    {
        return $this->id;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getSize(): ?CompanySize
    {
        return $this->size;
    }

    public function setSize(?CompanySize $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getTest(): ?AuditTestPhase
    {
        return $this->test;
    }

    public function setTest(?AuditTestPhase $test): self
    {
        $this->test = $test;

        return $this;
    }
}
