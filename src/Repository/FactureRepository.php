<?php

namespace App\Repository;

use App\Entity\Facture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Facture>
 */
class FactureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Facture::class);
    }

    /**
     * Récupère une liste de factures pour une page donnée avec une limite définie.
     * * @param int $page Le numéro de la page.
     * @param int $limit Le nombre de résultats par page.
     * @return Facture[] Returns an array of Facture objects
     */
    public function findWithPagination(int $page, int $limit): array
    {
        return $this->createQueryBuilder('f')
            ->orderBy('f.dateEmission', 'DESC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Compte le nombre total de factures.
     * * @return int Returns the total number of Facture objects
     */
    public function countAll(): int
    {
        return $this->createQueryBuilder('f')
            ->select('count(f.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
