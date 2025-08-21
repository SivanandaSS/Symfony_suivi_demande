<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\User;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;


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
}
