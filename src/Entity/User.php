<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 * @UniqueEntity("email", message="This user already exists")
 * @Hateoas\Relation(
 *     "self",
 *     href = @Hateoas\Route(
 *          "user_details",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *     ),
 *     exclusion = @Hateoas\Exclusion(groups={"users_list"}),
 * )
 * @Hateoas\Relation(
 *     "users_list",
 *     href = @Hateoas\Route(
 *          "users",
 *          absolute = true
 *     ),
 *     exclusion = @Hateoas\Exclusion(groups={"user_details"}),
 * )
 * @Hateoas\Relation(
 *     "Create user",
 *     href = @Hateoas\Route(
 *          "user_create",
 *          absolute = true
 *     ),
 *     exclusion = @Hateoas\Exclusion(groups={"users_list"}),
 * )
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Serializer\Groups({"users_list", "user_details"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank(message="You must enter 'email'")
     * @Assert\Email(message="Email is not valid")
     * @Serializer\Groups({"users_list", "user_details"})
     */
    private $email;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Type("\DateTimeInterface")
     * @Serializer\Groups({"user_details"})
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class, inversedBy="users")
     * @ORM\JoinColumn(nullable=false)
     */
    private $client;

    public function __construct()
    {
        $this->createdAt = new \DateTime('NOW');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function setClient(Client $client): self
    {
        $this->client = $client;

        return $this;
    }
}
