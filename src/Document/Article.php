<?php
namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Validator\Constraints as Assert;

#[MongoDB\Document]
class Article
{
    #[MongoDB\Id]
    private $id;

    #[MongoDB\Field(type: "string")]
    #[Assert\NotBlank]
    private $title;

    #[MongoDB\Field(type: "string")]
    #[Assert\NotBlank]
    private $content;

    #[MongoDB\Field(type: "date")]
    private $publishedAt;

    #[MongoDB\ReferenceOne(targetDocument: User::class)]
    private $author;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function getPublishedAt(): ?\DateTime
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(\DateTime $publishedAt): self
    {
        $this->publishedAt = $publishedAt;
        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(User $author): self
    {
        $this->author = $author;
        return $this;
    }
}