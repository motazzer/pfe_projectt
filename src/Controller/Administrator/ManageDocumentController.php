<?php
namespace App\Controller\Administrator;

use App\Entity\CourseDocument;
use App\Message\TransformTextToVectors;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class ManageDocumentController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/api/administrator/documents', name:'admin_documents_index', methods: ['GET'])]
    public function index(): Response
    {
        $documents = $this->entityManager->getRepository(CourseDocument::class)->findAll();

        $documentData = array_map(function ($documents) {
            return [
                'id' => $documents->getId(),
                'title' => $documents->getTitle(),
                'created_by' => $documents->getUploadedByEmail(),
                'createdAt' => $documents->getCreatedAt()->format('Y-m-d H:i:s'),
                'content' => $documents->getContent(),
                'status' => $documents->getStatus(),
            ];
        }, $documents);

        return $this->json($documentData);
    }
    #[Route('/api/administrator/documents/details/{id}', name: 'admin_document_details', methods: ['GET'])]
    public function getDocumentDetails($id): Response
    {
        $document = $this->entityManager->getRepository(CourseDocument::class)->find($id);
        $documentData = [
            'id' => $document->getId(),
            'title' => $document->getTitle(),
            'created_by' => $document->getUploadedByEmail(),
            'createdAt' => $document->getCreatedAt()->format('Y-m-d H:i:s'),
            'content' => $document->getContent(),
            'status' => $document->getStatus(),
        ];
        return $this->json($documentData);
    }
    #[Route('/api/administrator/documents/{id}', name: 'admin_documents_update', methods: ['PUT'])]
    public function update(Request $request, $id): Response
    {
        $document = $this->entityManager->getRepository(CourseDocument::class)->find($id);
        $data = json_decode($request->getContent(), true);

        if (isset($data['title'])) {
            $document->setTitle($data['title']);
        }
        if (isset($data['content'])) {
            $document->setContent($data['content']);
        }
        $this->entityManager->flush();
        return $this->json(['message' => 'Document updated successfully']);
    }

    #[Route('/api/administrator/documents/{id}', name: 'admin_document_update_details', methods: ['GET'])]
    public function get_Document_to_update_Details($id): Response
    {
        $document = $this->entityManager->getRepository(CourseDocument::class)->find($id);
        $documentData = [
            'title' => $document->getTitle(),
            'content' => $document->getContent(),
        ];
        return $this->json($documentData);
    }

    #[Route('/api/administrator/documents/{id}', name:'admin_documents_delete', methods: ['DELETE'])]
    public function delete($id): Response
    {
        $document = $this->entityManager->getRepository(CourseDocument::class)->find($id);
        $this->entityManager->remove($document);
        $this->entityManager->flush();

        return $this->json(['message' => 'Document supprimé']);
    }
    #[Route('/api/administrator/documents/{id}/verify', name:'admin_documents_verify', methods: ['POST'])]
    public function verifyDocument(MessageBusInterface $bus, $id): JsonResponse
    {
        $document = $this->entityManager->getRepository(CourseDocument::class)->find($id);

        $message = new TransformTextToVectors($document->getId());
        $bus->dispatch($message);

        $document->setStatus('verified');
        $this->entityManager->flush();

        return $this->json(['message' => 'Document verified successfully and content transformed into vectors']);
    }

}