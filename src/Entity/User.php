<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 * @UniqueEntity("username", message="This user already exists")
 * @Hateoas\Relation(
 *     "user_details",
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
 *     exclusion = @Hateoas\Exclusion(groups={"user_details", "add_user"}),
 * )
 * @Hateoas\Relation(
 *     "delete_user",
 *     href = @Hateoas\Route(
 *          "delete_user",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *     ),
 *     exclusion = @Hateoas\Exclusion(groups={"user_details", "users_list", "add_user"}),
 * )
 * @Hateoas\Relation(
 *     "add_user",
 *     href = @Hateoas\Route(
 *          "add_user",
 *          absolute = true
 *     ),
 *     exclusion = @Hateoas\Exclusion(groups={"users_list", "add_user", "user_details"}),
 * )
 */
class User implements UserInterface
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
     * @Assert\NotBlank(message="You must enter 'username'")
     * @Serializer\Groups({"users_list", "user_details", "add_user"})
     */
    private $username;

    /**
     * @ORM\Column(type="string")
     * @Assert\Length(min="4")
     * @Assert\NotBlank(message="You must enter a 'password'")
     * @Serializer\Groups({"add_user"})
     */
    private $password;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Type("\DateTimeInterface")
     * @Serializer\Groups({"user_details"})
     * @Serializer\Groups({"add_user", "user_details"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="json")
     * @Serializer\Groups({"user_details"})
     */
    private $roles = [];

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

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPassword()
    {
        return (string) $this->password;
    }

    public function getSalt()
    {
    }

    public function eraseCredentials()
    {
    }
}
