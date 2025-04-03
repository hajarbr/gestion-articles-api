<?php
namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[MongoDB\Document]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[MongoDB\Id]
    private $id;

    #[MongoDB\Field(type: "string")]
    #[Assert\NotBlank]
    private $name;

    #[MongoDB\Field(type: "string")]
    #[Assert\NotBlank]
    #[Assert\Email]
    private $email;

    #[MongoDB\Field(type: "string")]
    private $password;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
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

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function eraseCredentials(): void
    {
        // Clear sensitive data if any
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }
}