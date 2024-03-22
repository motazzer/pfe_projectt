<?php

namespace App\Controller\Administrator;

use App\Entity\CourseDocument;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Dashboard extends AbstractController
{
    private EntityManagerInterface $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
        #[Route('/api/count-users', name: 'count_users', methods: ['GET'])]
        public function countUsers(): Response
        {
            $userCount = $this->entityManager->getRepository(User::class)->count([]);
            return $this->json(['userCount' => $userCount]);
        }

        #[Route('/api/count-documents', name: 'count_documents', methods: ['GET'])]
        public function countDocuments(): Response
        {
            $documentCount = $this->entityManager->getRepository(CourseDocument::class)->count([]);
            return $this->json(['documentCount' => $documentCount]);
        }

}