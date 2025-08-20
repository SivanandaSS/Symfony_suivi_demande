<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

final class RegisterController extends AbstractController
{
    #[Route('api/register', name: 'api_register', methods: ['POST'])]
    public function register(Request $request,
    EntityManagerInterface $entityManager,
    UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['email'], $data['password'])) {
            return new JsonResponse(['error' => 'Invalid data'], 400);
        }

        $user = new User();
        $user->setEmail($data['email']);
        $user->setRoles($data['roles']);
        $user->setNom($data['nom']);
        $user->setPrénom($data['prénom']);
        $user->setPassword(
            $passwordHasher->hashPassword($user, $data['password'])
        );

        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse(['message' => 'User registered successfully'], 201);
    }
}
