<?php
namespace App\Controller;

use App\Entity\Devis;
use App\Repository\FactureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class DevisAcceptController extends AbstractController
{
    public function __invoke(Devis $data, EntityManagerInterface $em, FactureRepository $factureRepository, Request $request)
    {
        // Mettre à jour le statut du devis
        $data->setStatut('Accepter');
        $em->persist($data);
        $em->flush();

        // Récupérer la facture générée par le trigger (via devis_id)
        $facture = $factureRepository->findOneBy(['devis' => $data->getId()]);

        $factureId = $facture ? $facture->getId() : null;

        return new JsonResponse([
            'devisId' => $data->getId(),
            'factureId' => $factureId,
        ]);
    }
}