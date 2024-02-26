<?php

namespace App\Controller;

use App\Entity\CourseDocument;
use App\Repository\CourseDocumentRepository;
use OpenAI;
use Smalot\PdfParser\Parser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
    public function upload(Request $request): Response
    {

        $file = $request->files->get('file');
        if (!$file) {
            return $this->json(['message' => 'No file uploaded'], Response::HTTP_BAD_REQUEST);
        }
        $content = $this->extractTextFromPDF($file);
        $cleancontent = $this->cleanText($content);

        $vectors = $this->transformTextToVectors($cleancontent);

        $courseDocument = new CourseDocument();
        $courseDocument->setTitle($file->getClientOriginalName());
        $courseDocument->setContent($content);
        $user = $this->getUser();
        $courseDocument->setUser($user);
        $courseDocument->setCreatedAt(new \DateTimeImmutable());
        $courseDocument->setVectors($vectors);
        $entityManager = $this->managerRegistry->getManager();
        $entityManager->persist($courseDocument);
        $entityManager->flush();

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

    private function extractTextFromPDF($file): string
    {
        $parser = new Parser();
        $pdf = $parser->parseFile($file->getPathname());
        $text = '';
        foreach ($pdf->getPages() as $page) {
            $text .= $page->getText();
        }
        return $text;
    }


    private function transformTextToVectors($content): array
    {

        $apiKey = $_ENV['OPENAI_API_KEY'] ?? null;
        if (!$apiKey) {
            throw new \RuntimeException('OpenAI API key not found in environment variables.');
        }
        $client = OpenAI::client($apiKey);

        $response = $client->embeddings()->create([
            'model' => 'text-similarity-babbage-001',
            'input' => $content,
        ]);
        $vectors = [];
        foreach ($response->embeddings as $embedding) {
            $vectors[] = $embedding->embedding;
        }
        return $vectors;
    }

    private function cleanText(string $text): string
    {

        $text = preg_replace('/[^a-zA-Z0-9\s]/', '', $text);
        $text = strtolower($text);
        $text = preg_replace('/\s+/', ' ', $text);
        $stopWords = [
            'afin', 'ainsi', 'alors', 'au', 'aucuns', 'aussi', 'autre', 'aux', 'avant', 'avec', 'ça', 'car',
            'ce', 'ceci', 'cela', 'celà', 'ces', 'c\'est', 'cet', 'cette', 'ceux', 'chaque', 'ci', 'comme',  'dans',
            'dedans', 'dehors', 'depuis', 'des', 'deux', 'devrait', 'doit', 'donc', 'dos', 'du',
            'd\'un', 'd\'une', 'elle', 'elles', 'en', 'encore', 'ensuite', 'es', 'essai', 'est', 'et', 'étaient',
            'étais', 'était', 'étant', 'état', 'etc', 'été', 'ete', 'étée', 'étées', 'êtes', 'étés', 'étiez', 'étions', 'être',
            'eu', 'eux', 'fait', 'faites', 'fois', 'font', 'force', 'fûmes', 'furent', 'fus', 'fusse', 'fussent', 'fusses',
            'fussiez', 'fussions', 'fut', 'fût', 'fûtes', 'grâce', 'haut', 'hors', 'ici', 'il', 'ils', 'ils4les', 'je', 'juste',
            'la', 'là', 'le', 'les', 'leur', 'leurs', 'ma', 'mais', 'meme', 'même', 'mes', 'mieux', 'mine', 'moins',
            'mon', 'mot', 'ni', 'nommés', 'nos', 'notre', 'nous', 'nouveaux', 'ou', 'où', 'par', 'parce', 'parole', 'pas',
             'peu', 'peut', 'pièce', 'plupart', 'pour', 'sa', 'sans', 'sera', 'serai', 'seraient', 'serais', 'serait', 'seras', 'serez', 'seriez', 'serions',
            'serons', 'seront', 'ses', 'seulement', 'si', 'sien', 'soi', 'soient', 'sois', 'soit', 'sommes', 'son', 'sont',
            'sous', 'soyez', 'soyons', 'suis', 'sujet', 'sur', 'ta', 'tandis', 'tellement', 'tel', 'tels', 'tes', 'ton', 'tous',
            'tout', 'toute', 'toutes', 'tres', 'très', 'trop', 'tu', 'un', 'une', 'voie', 'voient', 'vont', 'vos',
            'votre', 'vous', 'vu'
        ];
        $text = str_replace($stopWords, '', $text);

        return trim($text);
    }

}
