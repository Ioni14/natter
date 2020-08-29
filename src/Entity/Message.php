<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity
 */
class Message
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     *
     * @Groups({"read"})
     */
    private ?int $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Space")
     * @ORM\JoinColumn(nullable=false)
     *
     * @Groups({"read"})
     */
    private Space $space;

    /**
     * @ORM\Column(type="string", length=30)
     *
     * @Groups({"read"})
     */
    private string $author;

    /**
     * @ORM\Column(type="string", length=1024)
     *
     * @Groups({"read"})
     */
    private string $text;

    /**
     * @ORM\Column(type="datetimetz_immutable")
     */
    private \DateTimeInterface $createdAt;

    public function __construct(?int $id, Space $space, string $author, string $text, \DateTimeInterface $createdAt)
    {
        $this->id = $id;
        $this->space = $space;
        $this->author = $author;
        $this->text = $text;
        $this->createdAt = $createdAt;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSpace(): Space
    {
        return $this->space;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }
}
