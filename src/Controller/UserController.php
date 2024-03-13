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

    #[Route('/api/user', name: 'user_show', methods: ['GET'])]
    public function showUserProfile(): JsonResponse
    {
        $user = $this->getUser();
        $usersData = [
                'firstname' => $user->getFirstname(),
                'lastname' => $user->getLastname(),
                'email' => $user->getEmail(),
                'createdAt' => $user->getCreatedAt()->format('Y-m-d H:i:s'),
                'uploadedFilesCount' => $user->getUploadedFilesCount(),
            ];
        return $this->json($usersData);
    }

    #[Route('/api/update-user', name: 'user_update', methods: ['PUT'])]
    public function updateUserProfile(Request $request): JsonResponse
    {
        $user = $this->getUser();
        $data = json_decode($request->getContent(), true);

        if (isset($data['firstname'])) {
            $user->setFirstname($data['firstname']);
        }
        if (isset($data['lastname'])) {
            $user->setLastname($data['lastname']);
        }
        if (isset($data['email'])) {
            $user->setEmail($data['email']);
        }

        $user->setPassword($data['password']);

        $this->entityManager->flush();
        return $this->json(['message' => 'Profile updated successfully']);
    }

}
