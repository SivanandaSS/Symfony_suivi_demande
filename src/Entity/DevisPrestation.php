<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\DevisPrestationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: DevisPrestationRepository::class)]
#[ApiResource]
class DevisPrestation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Groups(['devis:list', 'devis:item'])]
    private ?string $quantity = null;

    #[ORM\ManyToOne(inversedBy: 'devisPrestations')]
    private ?Devis $devis = null;

    #[ORM\ManyToOne(inversedBy: 'devisPrestations')]
    #[Groups(['devis:list', 'devis:item'])]
    private ?Prestation $prestation = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Groups(['devis:list', 'devis:item'])]
    private ?string $soustotal = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantity(): ?string
    {
        return $this->quantity;
    }

    public function setQuantity(string $quantity): static
    {
        $this->quantity = $quantity;
        $this->updateSousTotal();

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

    public function getPrestation(): ?Prestation
    {
        return $this->prestation;

    }

    public function setPrestation(?Prestation $prestation): static
    {
        $this->prestation = $prestation;
        $this->updateSousTotal();
        
        return $this;
    }

    public function getSoustotal(): ?string
    {
        return $this->soustotal;
    }

    public function setSoustotal(string $soustotal): static
    {
        $this->soustotal = $soustotal;

        return $this;
    }

    private function updateSousTotal(): void
    {
        if ($this->prestation && $this->quantity !== null) {
            $this->soustotal = bcmul($this->quantity, $this->prestation->getPu(), 2);
        }
    }

    public function getPu(): ?string
    {
        return $this->prestation?->getPu();
    }

    public function __toString(): string
    {
    return sprintf("Prestation #%d (PU: %s, Qte: %s)", 
        $this->getId() ?? 0,
        $this->getPu() ?? "null",
        $this->getQuantity() ?? "null"
    );
    }
}
