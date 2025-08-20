<?php

namespace App\Entity;

use App\Repository\DemandeRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: DemandeRepository::class)]

#[ApiResource(
    operations: [
        new Get(normalizationContext: ['groups' => 'demande:item']),
        new GetCollection(normalizationContext: ['groups' => 'demande:list'])
    ],
    order: ['id' => 'DESC'],
    paginationEnabled: false,
)]


class Demande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['demande:list', 'demande:item'])]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Groups(['demande:list', 'demande:item'])]
    private ?string $nom = null;

    #[ORM\Column(length: 100)]
    #[Groups(['demande:list', 'demande:item'])]
    private ?string $prenom = null;

    #[ORM\Column(length: 200)]
    #[Groups(['demande:list', 'demande:item'])]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'demandes')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['demande:list', 'demande:item'])]
    private ?Category $category = null;

    #[ORM\OneToOne(inversedBy: 'demande', cascade: ['persist', 'remove'])]
    #[Groups(['demande:list', 'demande:item'])]
    private ?Devis $devis = null;

    #[ORM\ManyToOne(inversedBy: 'demandes')]
    #[Groups(['demande:list', 'demande:item'])]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getCategory(): ?category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getDevis(): ?Devis
    {
        return $this->devis;
    }

    public function setDevis(?Devis $devis): static
    {
        $this->devis = $devis;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function __toString(): string
    {
        return $this->nom;
    }
}
