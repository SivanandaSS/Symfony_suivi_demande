<?php

namespace App\Entity;

use App\Repository\FactureRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: FactureRepository::class)]
#[ApiResource(
    operations: [
        new Get(normalizationContext: ['groups' => 'facture:item']),
        new GetCollection(normalizationContext: ['groups' => 'facture:list'])
    ],
    order: ['id' => 'DESC'],
    paginationEnabled: false,
)]

class Facture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(mappedBy: 'facture', cascade: ['persist', 'remove'])]
    private ?Devis $devis = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['facture:list', 'facture:item'])]
    private ?string $facture = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDevis(): ?Devis
    {
        return $this->devis;
    }

    public function setDevis(?Devis $devis): static
    {
        // unset the owning side of the relation if necessary
        if ($devis === null && $this->devis !== null) {
            $this->devis->setFacture(null);
        }

        // set the owning side of the relation if necessary
        if ($devis !== null && $devis->getFacture() !== $this) {
            $devis->setFacture($this);
        }

        $this->devis = $devis;

        return $this;
    }

    public function getFacture(): ?string
    {
        return $this->facture;
    }

    public function setFacture(?string $facture): static
    {
        $this->facture = $facture;

        return $this;
    }


}
