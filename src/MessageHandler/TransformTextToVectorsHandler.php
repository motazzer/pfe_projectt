<?php

namespace App\MessageHandler;

use App\Entity\CourseDocument;
use App\Message\TransformTextToVectors;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Exception\RequestException;
use OpenAI\Client;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class TransformTextToVectorsHandler implements MessageHandlerInterface
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(TransformTextToVectors $message)
    {
        $document = $this->entityManager->getRepository(CourseDocument::class)->find($message->getDocumentId());

        $cleanedText = $this->cleanText($document->getContent());
        $embeddings = $this->transformTextToVectors($cleanedText);

        $document->setVectors($embeddings);
        $this->entityManager->flush();
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

}
