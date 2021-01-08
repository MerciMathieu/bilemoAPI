<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 */
class Product
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"products_list", "product_details"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Groups({"products_list", "product_details"})
     */
    private $modelName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Groups({"product_details"})
     */
    private $modelRef;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     * @Groups({"product_details"})
     */
    private $memory;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Groups({"products_list", "product_details"})
     */
    private $color;

    /**
     * @ORM\Column(type="float")
     * @Assert\NotBlank()
     * @Groups({"products_list", "product_details"})
     */
    private $price;

    /**
     * @ORM\Column(type="text", length=255)
     * @Assert\NotBlank()
     * @Groups({"product_details"})
     */
    private $description;

    /**
     * @ORM\Column(type="text", length=255, nullable=true)
     * @Assert\Url()
     * @Groups({"products_list", "product_details"})
     */
    private $urlImage;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getModelName(): ?string
    {
        return $this->modelName;
    }

    public function setModelName(string $modelName): self
    {
        $this->modelName = $modelName;

        return $this;
    }

    public function getModelRef(): ?string
    {
        return $this->modelRef;
    }

    public function setModelRef(string $modelRef): self
    {
        $this->modelRef = $modelRef;

        return $this;
    }

    public function getMemory(): ?int
    {
        return $this->memory;
    }

    public function setMemory(int $memory): self
    {
        $this->memory = $memory;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getUrlImage(): ?string
    {
        return $this->urlImage;
    }

    public function setUrlImage(?string $urlImage): self
    {
        $this->urlImage = $urlImage;

        return $this;
    }
}
