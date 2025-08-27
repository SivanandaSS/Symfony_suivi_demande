<?php

namespace App\Controller;

use ApiPlatform\Metadata\Post;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\User;
use App\Entity\Devis;
use App\Entity\Demande;
use App\Entity\Facture;
use Twig\Environment;
use App\Controller\FactureController;
use App\Repository\FactureRepository;

use App\Repository\DevisRepository;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpFoundation\JsonResponse;

final class ApiLoginController extends AbstractController
{
    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function index(#[CurrentUser] ?User $user, EntityManagerInterface $entityManager ): Response
    {
        if (null === $user) {
            return $this->json([
                'message' => 'missing credentials',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $token = Uuid::v4()->toRfc4122(); // somehow create an API token for $user

        $user->setApiKey($token);

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json([
            'id'    => $user->getId(),
            'user'  => [
                'prénom' => $user->getPrénom(),
                'nom'    => $user->getNom(),
                'email'  => $user->getUserIdentifier(),
                'roles' => $user->getRoles(),
             ],
            'token' => $token,
        ]);
    }

    #[Route(path: '/api/logout', name: 'api_logout')]
    public function logout(#[CurrentUser] ?User $user, EntityManagerInterface $entityManager ): Response
    {
        if (null === $user) {
            return $this->json([
                'message' => 'missing credentials',
            ], Response::HTTP_UNAUTHORIZED);
        }
    
        $user->setApiKey(null);
        $entityManager->persist($user);
        $entityManager->flush();
    
        return $this->json([
            'message' => 'Logged out successfully'
        ]);
    }

    #[Route(path: '/api/backoffice/{user}', name: 'api_backoffice')]
    public function backoffice(EntityManagerInterface $entityManager, int $user ): Response
    {
        $conn = $entityManager->getConnection();

        $sql = '
        SELECT  demande.id AS id, `nom`, `prenom`, `description`, demande.devis_id, `user_id`, devis.total, devis.numero, `statut`, `date_devis`, facture.id AS factureId, `facture`, facture.paiement AS paiement FROM `demande` LEFT JOIN devis ON (devis.id=demande.devis_id) LEFT JOIN facture ON (facture.devis_id=devis.id) WHERE user_id=:user;
            ';

        $resultSet = $conn->executeQuery($sql, ['user' => $user]);
    
        return $this->json(
            $resultSet->fetchAllAssociative()
        );
    }

    #[Route(path: '/api/devis/{id}/accept', name: 'api_devis_accept', methods: ['POST'])]
    public function acceptDevis(
        EntityManagerInterface $entityManager, 
        DevisRepository $devisRepository, 
        FactureRepository $factureRepository,
        Environment $twig,
        int $id ): Response
    {
        $devis = $devisRepository->find($id);
        
        // MAJ le statut du devis
        $devis->setStatut('Accepté');
        
        $entityManager->persist($devis);
        $entityManager->flush();

        // Genere la facture save en utilisant la méthode réutilisable
        $facture = $factureRepository->findOneBy(['devis' => $devis -> getId()]);

        $factureId = $facture ? $facture -> getId() : null;

        return new JsonResponse([
            'factureId' => $factureId,
            'devisId' => $devis ->getId(),
        ]);
    }
 
 } 
