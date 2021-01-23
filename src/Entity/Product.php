<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 * @Hateoas\Relation(
 *     "self",
 *     href = @Hateoas\Route(
 *          "product_details",
 *          parameters = { "productId" = "expr(object.getId())" },
 *          absolute = true
 *     ),
 *     exclusion = @Hateoas\Exclusion(groups={"products_list"}),
 * )
 * @Hateoas\Relation(
 *     "products_list",
 *     href = @Hateoas\Route(
 *          "products",
 *          absolute = true
 *     ),
 *     exclusion = @Hateoas\Exclusion(groups={"product_details"}),
 * )
 */
class Product
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Serializer\Groups({"products_list", "product_details"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Serializer\Groups({"products_list", "product_details"})
     */
    private $modelName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Serializer\Groups({"product_details"})
     */
    private $modelRef;

    /**
     * @ORM\Column(type="integer")
     * @Serializer\Groups({"product_details"})
     */
    private $memory;

    /**
     * @ORM\Column(type="string", length=255)
     * @Serializer\Groups({"products_list", "product_details"})
     */
    private $color;

    /**
     * @ORM\Column(type="float")
     * @Serializer\Groups({"products_list", "product_details"})
     */
    private $price;

    /**
     * @ORM\Column(type="text", length=255)
     * @Serializer\Groups({"product_details"})
     */
    private $description;

    /**
     * @ORM\Column(type="text", length=255, nullable=true)
     * @Serializer\Groups({"products_list", "product_details"})
     */
    private $urlImage;

    public function getId(): int
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
