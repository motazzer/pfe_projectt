<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/api/users', name: 'user_list', methods: ['GET'])]
    public function listUsers(): JsonResponse
    {
        $users = $this->entityManager->getRepository(User::class)->findAll();
        $usersData = array_map(function ($user) {
            return [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'firstname' => $user->getFirstname(),
                'lastname' => $user->getLastname(),
                'createdAt' => $user->getCreatedAt()->format('Y-m-d H:i:s'),
                'uploadedFilesCount' => $user->getUploadedFilesCount(),
            ];
        }, $users);

        return $this->json($usersData);
    }

    #[Route('/api/users/{id}', name: 'user_show', methods: ['GET'])]
    public function showUser($id): JsonResponse
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);
        $usersData = array_map(function ($user) {
            return [
                'firstname' => $user->getFirstname(),
                'lastname' => $user->getLastname(),
                'email' => $user->getEmail(),
                'createdAt' => $user->getCreatedAt()->format('Y-m-d H:i:s'),
                'uploadedFilesCount' => $user->getUploadedFilesCount(),
            ];
        }, $user);
        return $this->json($usersData);
    }

    #[Route('/api/users/{id}', name: 'user_update', methods: ['PUT'])]
    public function updateUser($id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $user = $this->entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        $user->setFirstname($data['firstname']);
        $user->setLastname($data['lastname']);
        $user->setEmail($data['email']);
        $user->setPassword($data['password']);


        $this->entityManager->flush();

        return $this->json($user);
    }

    #[Route('/api/users/{id}', name: 'user_delete', methods: ['DELETE'])]
    public function deleteUser($id): JsonResponse
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return $this->json(['message' => 'Utilisateur supprimé']);
    }
}
