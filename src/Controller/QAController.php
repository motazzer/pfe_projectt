<?php

namespace App\Controller;

use App\Entity\CourseDocument;
use App\Repository\CourseDocumentRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Question;
use App\Entity\Answer;
use Symfony\Component\HttpFoundation\Request;



class QAController extends AbstractController
{

    private $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {

        $this->managerRegistry = $managerRegistry;
    }
    #[Route('/api/questions', name: 'question_create', methods: ['POST'])]
    public function createQuestion(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        $entityManager = $this->managerRegistry->getManager();
        $question = new Question();
        $question->setContent($data['content']);
        $user = $this->getUser();
        $question->setUser($user);
        $entityManager->persist($question);
        $entityManager->flush();
        return $this->json($question, Response::HTTP_CREATED);
    }

     #[Route('/api/question-answer/{id}', name: 'question_show', methods: ['GET'])]

    public function showQuestion(Question $question): Response
    {
        $data = [
            'question' => $question,
            'answers' => $question->getAnswers()->toArray()
        ];

        return $this->json($data, Response::HTTP_OK);
    }
    /**
    #[Route('/api/documents/{id}/answer', name: 'document_answer_create', methods: ['POST'])]
    public function createDocumentAnswer(Request $request, Question $question, CourseDocument $courseDocument): Response
    {
        $questionConten = $question->getContent();
        $documentConten = $courseDocument->getContent();
        $answer = $this->generateAnswerWithGPT4($documentConten, $questionConten);

        $entityManager = $this->managerRegistry->getManager();
        $newAnswer = new Answer();
        $newAnswer->setContent($answer);
        $newAnswer->setQuestion($question);
        $entityManager->persist($newAnswer);
        $entityManager->flush();

        return $this->Json(['answer' => $answer]);
    }
*/

    #[Route('/api/questions/{id}/answers', name: 'answer_create', methods: ['POST'])]
    public function createAnswer(Request $request, Question $question, CourseDocumentRepository $courseDocumentRepository): Response
    {
        $questionContent = $question->getContent();
        $courseDocuments = $courseDocumentRepository->findAll();

        $documentVectors = [];
        foreach ($courseDocuments as $courseDocument) {
            $vectors = $courseDocument->getVectors();
            $documentVectors[$courseDocument->getId()] = $vectors;
        }

        $questionVector = $this->getQuestionVector($questionContent);

        $mostRelevantDocumentId = $this->findMostRelevantDocument($questionVector, $documentVectors);

        $mostRelevantDocument = $courseDocumentRepository->find($mostRelevantDocumentId);
        $mostRelevantDocumentContent = $mostRelevantDocument->getContent();

        $answer = $this->generateAnswerWithGPT4($mostRelevantDocumentContent, $questionContent);

        $entityManager = $this->managerRegistry->getManager();
        $newAnswer = new Answer();
        $newAnswer->setContent($answer);
        $newAnswer->setQuestion($question);
        $entityManager->persist($newAnswer);
        $entityManager->flush();

        return $this->json($newAnswer, Response::HTTP_CREATED);
    }

    private function getQuestionVector(string $questionContent): array
    {
        $apiKey = $_ENV['OPENAI_API_KEY'] ?? null;
        if (!$apiKey) {
            throw new \RuntimeException('OpenAI API key not found in environment variables.');
        }

        $client = new \OpenAI\Client($apiKey);

        $tokens = explode(' ', $questionContent);

        $embeddings = [];
        foreach ($tokens as $token) {
            $response = $client->embeddings()->create([
                'inputs' => $token
            ]);

            $vector = $response['embedding'];
            $embeddings[] = $vector;
        }

        $questionVector = [];
        $numDimensions = count($embeddings[0]);
        for ($i = 0; $i < $numDimensions; $i++) {
            $sum = 0;
            foreach ($embeddings as $embedding) {
                $sum += $embedding[$i];
            }
            $questionVector[] = $sum / count($embeddings);
        }

        return $questionVector;
    }

    private function findMostRelevantDocument(array $questionVector, array $documentVectors): int
    {
        $mostRelevantDocumentId = null;
        $maxSimilarity = -1;

        foreach ($documentVectors as $documentId => $documentVector) {
            $similarity = $this->cosineSimilarity($questionVector, $documentVector);
            if ($similarity > $maxSimilarity) {
                $mostRelevantDocumentId = $documentId;
                $maxSimilarity = $similarity;
            }
        }
        return $mostRelevantDocumentId;
    }

    private function cosineSimilarity(array $vector1, array $vector2): float
    {
        $dotProduct = 0;
        $magnitude1 = 0;
        $magnitude2 = 0;

        for ($i = 0; $i < count($vector1); $i++) {
            $dotProduct += $vector1[$i] * $vector2[$i];
            $magnitude1 += $vector1[$i] ** 2;
            $magnitude2 += $vector2[$i] ** 2;
        }

        $magnitude1 = sqrt($magnitude1);
        $magnitude2 = sqrt($magnitude2);
        if ($magnitude1 && $magnitude2) {
            return $dotProduct / ($magnitude1 * $magnitude2);
        } else {
            return 0;
        }
    }

    private function generateAnswerWithGPT4(string $documentContent, string $questionContent): string
    {
        $apiKey = $_ENV['OPENAI_API_KEY'] ?? null;
        if (!$apiKey) {
            throw new \RuntimeException('OpenAI API key not found in environment variables.');
        }

        $openAI = new \OpenAI\Client($apiKey);

        $prompt = "Document: $documentContent Question: $questionContent";

        try {
            $response = $openAI->completions([
                'model' => 'text-davinci-003',
                'prompt' => $prompt,
                'max_tokens' => 100
            ]);

            return $response->choices[0]->text ?? 'No answer generated';
        } catch (\Exception $e) {
            return 'Error generating answer: ' . $e->getMessage();
        }
    }

}
