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
    order: ['id' => 'DESC'],
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

    /**
     * @var Collection<int, prestation>
     */
    #[ORM\ManyToMany(targetEntity: Prestation::class, inversedBy: 'devis')]
    #[Groups(['devis:list', 'devis:item'])]
    private Collection $prestation;

    #[ORM\OneToOne(inversedBy: 'devis', cascade: ['persist', 'remove'])]
    #[Groups(['devis:list', 'devis:item'])]
    private ?Facture $facture = null;

    public function __construct()
    {
        $this->prestation = new ArrayCollection();
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

    /**
     * @return Collection<int, prestation>
     */
    public function getPrestation(): Collection
    {
        return $this->prestation;
    }

    public function addPrestation(Prestation $prestation): static
    {
        if (!$this->prestation->contains($prestation)) {
            $this->prestation->add($prestation);
        }

        return $this;
    }

    public function removePrestation(Prestation $prestation): static
    {
        $this->prestation->removeElement($prestation);

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

}
