<?php

namespace App\MessageHandler;

use App\Entity\CourseDocument;
use App\Message\TransformTextToVectors;
use Doctrine\ORM\EntityManagerInterface;
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
        $vectors = $this->transformTextToVectors($cleanedText);

        $document->setVectors($vectors);
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

    private function transformTextToVectors(array $tokens): string
    {
        $binaryData = random_bytes(10);
        return $binaryData;
    }
    /**
    private function transformTextToVectors(string $content): string
    {
    // Placeholder for transforming text to vectors
    /**
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
    ********************
$binaryData = random_bytes(10);
return $binaryData;
}
* */
}
