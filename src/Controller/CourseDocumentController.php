<?php

namespace App\Controller;

use App\Entity\CourseDocument;
use App\Message\ExtractCourseDocumentContent;
use App\Repository\CourseDocumentRepository;
use OpenAI;
use Random\RandomException;
use Smalot\PdfParser\Parser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;



class CourseDocumentController extends AbstractController{
    private $managerRegistry;
    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    #[Route('/api/course-documents', name: 'course_document_index', methods: ['GET'])]
    public function index(CourseDocumentRepository $courseDocumentRepository): Response
    {
        $courseDocuments = $courseDocumentRepository->findAll();
        return $this->json($courseDocuments);
    }

    #[Route('/api/course-documents/upload', name: 'course_document_upload', methods: ['POST'])]
    public function upload(Request $request,MessageBusInterface $bus): Response
    {
        $file = $request->files->get('file');

        if (!$file) {
            return $this->json(['message' => 'No file uploaded'], Response::HTTP_BAD_REQUEST);
        }

        $uploadsDirectory = $this->getParameter('uploads_directory');
        $fileName = md5(uniqid()) . '.' . $file->guessExtension();

        try {
            $file->move(
                $uploadsDirectory,
                $fileName
            );
        } catch (\Exception $e) {
            return $this->json(['message' => 'Failed to move the uploaded file'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $courseDocument = new CourseDocument();
        $courseDocument->setTitle($file->getClientOriginalName());
        $user = $this->getUser();
        $courseDocument->setUser($user);
        $courseDocument->setCreatedAt(new \DateTimeImmutable());
        $courseDocument->setStatus('unverified');
        $courseDocument->setFilePath($uploadsDirectory . '/' . $fileName);

        $entityManager = $this->managerRegistry->getManager();
        $entityManager->persist($courseDocument);
        $entityManager->flush();

        $message = new ExtractCourseDocumentContent($courseDocument->getId());
        $bus->dispatch($message);

        return $this->json($courseDocument, Response::HTTP_CREATED, [], ['groups' => 'course_document']);
    }

    #[Route('/api/course-documents/{id}', name: 'course_document_update', methods: ['PUT'])]
    public function update(Request $request, CourseDocument $courseDocument): Response
    {
        $data = json_decode($request->getContent(), true);
        $courseDocument->setTitle($data['title']);
        $courseDocument->setContent($data['content']);

        $entityManager = $this->managerRegistry->getManager();
        $entityManager->flush();

        return $this->json($courseDocument);
    }

    #[Route('/api/course-documents/{id}', name: 'course_document_delete', methods: ['DELETE'])]
    public function delete(CourseDocument $courseDocument): Response
    {
        $entityManager = $this->managerRegistry->getManager();
        $entityManager->remove($courseDocument);
        $entityManager->flush();

        return $this->json(['message' => 'Document deleted']);
    }

}
