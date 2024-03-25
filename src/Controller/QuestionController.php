<?php

namespace App\Controller;

use App\Entity\CourseDocument;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use GuzzleHttp\Exception\RequestException;
use OpenAI\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Question;
use App\Entity\Answer;
use Symfony\Component\HttpFoundation\Request;

class QuestionController extends AbstractController
{
    private $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    #[Route('/api/current-discussion', name: 'current_discussion', methods: ['GET'])]
    public function getCurrentDiscussion(Request $request): Response
    {
        $entityManager = $this->managerRegistry->getManager();
        $latestQuestion = $entityManager->getRepository(Question::class)->findOneBy([], ['id' => 'DESC']);
        $latestAnswer = $latestQuestion ? $latestQuestion->getAnswer()->getContent() : null;

        return $this->json([
            'question' => $latestQuestion ? $latestQuestion->getContent() : null,
            'answer' => $latestAnswer,
        ]);
    }

    #[Route('/api/ask', name: 'question_create', methods: ['POST'])]
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

        $cleanQuestion = $this->cleanText($question->getContent());
        $questionEmbeddings = $this->transformTextToVectors($cleanQuestion);

        $similarDocuments = $this->performSemanticSearch($questionEmbeddings);

        $answerContent = $this->generateAnswerWithGPT4($question->getContent(), $similarDocuments);

        $answer = new Answer();
        $answer->setContent($answerContent);
        $answer->setQuestion($question);
        $entityManager->persist($answer);
        $entityManager->flush();


        return $this->json([
                    'question' => $question->getContent(),
                    'answer' => $answerContent],
        Response::HTTP_CREATED);
    }

    private function cleanText(string $text): array
    {
        $text = mb_strtolower($text);
        $text = preg_replace('/[^a-z0-9\s.\p{L}]/u', '', $text);
        $text = preg_replace('/\s+/', ' ', $text);
        $lines = explode('.', $text);
        $lines = array_filter($lines, function($line) {
            return !is_numeric(trim($line));
        });
        $lines = array_map('trim', $lines);
        echo "lines :";
        print_r($lines);
        return $lines;
    }

    private function transformTextToVectors(array $texts): array
    {
        $apiKey = $_ENV['OPENAI_API_KEY'] ?? null;
        if (!$apiKey) {
            throw new \RuntimeException('OpenAI API key not found in environment variables.');
        }
        $client = new Client($apiKey);

        $embeddings = [];
        foreach ($texts as $text) {
            try {
                $response = $client->embeddings()->create([
                    'model' => 'text-similarity-babbage-001',
                    'input' => $text,
                ]);

                foreach ($response->embeddings as $embedding) {
                    $embeddings[] = $embedding->embedding;
                }

            } catch (RequestException $e) {
                continue;
            }
        }

        return $embeddings;
    }

    private function performSemanticSearch(array $questionEmbeddings): ?string
    {
        $entityManager = $this->managerRegistry->getManager();

        $mostSimilarDocumentContent = $entityManager->getRepository(CourseDocument::class)
            ->createQueryBuilder('cd')
            ->addSelect('SIMILARITY(cd.vectors, :questionEmbeddings) AS similarity')
            ->addSelect('cd.content AS content')
            ->setParameter('questionEmbeddings', $questionEmbeddings)
            ->orderBy('similarity', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $mostSimilarDocumentContent['content'];
    }


    private function generateAnswerWithGPT4(string $question, string $courseDocumentContent): string
    {
        $apiKey = $_ENV['OPENAI_API_KEY'] ?? null;
        if (!$apiKey) {
            throw new \RuntimeException('OpenAI API key not found in environment variables.');
        }
        $client = new Client($apiKey);

        $prompt = "for this question" . $question . "\n" . "give me answer from this " .$courseDocumentContent . "\nQ:";

        try {
            $response = $client->completions()->create([
                'model' => 'text-davinci-003',
                'prompt' => $prompt,
                'max_tokens' => 100,
                'temperature' => 0.7,
                'stop' => 'Q:',
            ]);


            return $response->choices[0]->text;

        } catch (Exception $e) {
            throw new \RuntimeException('OpenAI request failed: ' . $e->getMessage());
        }
    }


}
