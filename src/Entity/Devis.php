<?php

namespace App\Entity;

use App\Repository\DevisRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: DevisRepository::class)]

#[ApiResource(
    operations: [
        new Get(normalizationContext: ['groups' => 'devis:item']),
        new GetCollection(normalizationContext: ['groups' => 'devis:list'])
    ],
    order: ['numero' => 'DESC'],
    paginationEnabled: false,
)]



class Devis
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['devis:list', 'devis:item'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Groups(['devis:list', 'devis:item'])]
    private ?string $total = null;

    #[ORM\OneToOne(mappedBy: 'devis', cascade: ['persist', 'remove'])]
    #[Groups(['devis:list', 'devis:item'])]
    private ?Demande $demande = null;

    #[ORM\OneToOne(inversedBy: 'devis', cascade: ['persist', 'remove'])]
    #[Groups(['devis:list', 'devis:item'])]
    private ?Facture $facture = null;

    /**
     * @var Collection<int, DevisPrestation>
     */
    #[ORM\OneToMany(targetEntity: DevisPrestation::class, mappedBy: 'devis', cascade: ["persist", "remove"], orphanRemoval: true)]
    #[Groups(['devis:list', 'devis:item'])]
    private Collection $devisPrestations;

    #[ORM\Column(length: 255)]
    #[Groups(['devis:list', 'devis:item'])]
    private ?string $numero = null;

    #[ORM\Column(length: 255)]
    #[Groups(['devis:list', 'devis:item'])]
    private ?string $statut = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['devis:list', 'devis:item'])]
    private ?\DateTime $date = null;

    public function __construct()
    {
        $this->prestation = new ArrayCollection();
        $this->devisPrestations = new ArrayCollection();
        $this->date = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDemande(): ?Demande
    {
        return $this->demande;
    }

    public function setDemande(?Demande $demande): static
    {
        // unset the owning side of the relation if necessary
        if ($demande === null && $this->demande !== null) {
            $this->demande->setDevis(null);
        }

        // set the owning side of the relation if necessary
        if ($demande !== null && $demande->getDevis() !== $this) {
            $demande->setDevis($this);
        }

        $this->demande = $demande;

        return $this;
    }

    public function getFacture(): ?Facture
    {
        return $this->facture;
    }

    public function setFacture(?Facture $facture): static
    {
        $this->facture = $facture;

        return $this;
    }

    /**
     * @return Collection<int, DevisPrestation>
     */
    public function getDevisPrestations(): Collection
    {
        return $this->devisPrestations;
    }

    public function addDevisPrestation(DevisPrestation $devisPrestation): static
    {
        if (!$this->devisPrestations->contains($devisPrestation)) {
            $this->devisPrestations->add($devisPrestation);
            $devisPrestation->setDevis($this);
        }

        return $this;
    }

    public function removeDevisPrestation(DevisPrestation $devisPrestation): static
    {
        if ($this->devisPrestations->removeElement($devisPrestation)) {
            // set the owning side to null (unless already changed)
            if ($devisPrestation->getDevis() === $this) {
                $devisPrestation->setDevis(null);
            }
        }

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

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): static
    {
        $this->date = $date;

        return $this;
    }

    

}
