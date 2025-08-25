<?php

namespace App\Entity;

use App\Repository\FactureRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
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

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['facture:list', 'facture:item'])]
    private ?string $facture = null;

    #[ORM\Column(length: 255)]
    private ?string $numero = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $montant = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $dateEmission = null;

    #[ORM\Column]
    private ?bool $paiement = null;

    #[ORM\OneToOne(inversedBy: 'facture', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Devis $devis = null;

    /**
     * @var Collection<int, FacturePrestation>
     */
    #[ORM\OneToMany(targetEntity: FacturePrestation::class, mappedBy: 'facture', orphanRemoval: true)]
    private Collection $facture_prestation;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $total = null;

    public function __construct()
    {
        $this->facture_prestation = new ArrayCollection();
    }



    public function getId(): ?int
    {
        return $this->id;
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

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(string $numero): static
    {
        $this->numero = $numero;

        return $this;
    }

    public function getMontant(): ?string
    {
        return $this->montant;
    }

    public function setMontant(string $montant): static
    {
        $this->montant = $montant;

        return $this;
    }

    public function getDateEmission(): ?\DateTime
    {
        return $this->dateEmission;
    }

    public function setDateEmission(\DateTime $dateEmission): static
    {
        $this->dateEmission = $dateEmission;

        return $this;
    }

    public function isPaiement(): ?bool
    {
        return $this->paiement;
    }

    public function setPaiement(bool $paiement): static
    {
        $this->paiement = $paiement;

        return $this;
    }

    public function getDevis(): ?Devis
    {
        return $this->devis;
    }

    public function setDevis(Devis $devis): static
    {
        $this->devis = $devis;

        return $this;
    }

    /**
     * @return Collection<int, FacturePrestation>
     */
    public function getFacturePrestation(): Collection
    {
        return $this->facture_prestation;
    }

    public function addFacturePrestation(FacturePrestation $facturePrestation): static
    {
        if (!$this->facture_prestation->contains($facturePrestation)) {
            $this->facture_prestation->add($facturePrestation);
            $facturePrestation->setFacture($this);
        }

        return $this;
    }

    public function removeFacturePrestation(FacturePrestation $facturePrestation): static
    {
        if ($this->facture_prestation->removeElement($facturePrestation)) {
            // set the owning side to null (unless already changed)
            if ($facturePrestation->getFacture() === $this) {
                $facturePrestation->setFacture(null);
            }
        }

        return $this;
    }

    public function getTotal(): ?string
    {
        return $this->total;
    }

    public function setTotal(string $total): static
    {
        $this->total = $total;

        return $this;
    }



}
