<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\ArticleRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
#[ApiResource]
class Article
{
    #[ORM\Id]
    #[ORM\Column(unique: true)]
    #[ORM\GeneratedValue]
    private ?int $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $title;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private string $content;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private string $image;

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    private int $fav;

    #[ORM\Column(type: 'integer')]
    private int $price;

    #[ORM\Column(type: 'integer')]
    private int $tva;


    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): void
    {
        $this->category = $category;
    }

    public function getTva(): int
    {
        return $this->tva;
    }

    public function setTva(int $tva): void
    {
        $this->tva = $tva;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function setPrice(int $price): void
    {
        $this->price = $price;
    }

    public function getFav(): int
    {
        return $this->fav;
    }

    public function setFav(int $fav): void
    {
        $this->fav = $fav;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function setImage(string $image): void
    {
        $this->image = $image;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

}
