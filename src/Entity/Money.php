<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\MoneyRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MoneyRepository::class)]
#[ApiResource]
class Money
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $account = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id)
    {
        $this->id = $id;

        return $this;
    }

    public function getAccount(): ?float
    {
        return $this->account;
    }

    public function setAccount(float $account)
    {
        $this->account = $account;

        return $this;
    }

    public function getUser(): ?user
    {
        return $this->user;
    }

    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }
}
