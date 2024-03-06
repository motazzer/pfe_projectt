<?php

namespace App\MessageHandler;

use App\Entity\CourseDocument;
use App\Message\TransformTextToVectors;
use Doctrine\ORM\EntityManagerInterface;
use Phpml\Tokenization\WordTokenizer;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use voku\helper\StopWords;
use voku\helper\StopWordsLanguageNotExists;

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

        $document->setGg($cleanedText);
        $document->setVectors($vectors);
        $this->entityManager->flush();
    }

    /**
     * @throws StopWordsLanguageNotExists
     */
    private function cleanText(string $text): array
    {
        $cleanedText = preg_replace('/[^\p{L}\p{N}\s]/u', '', $text);

        $lower = strtolower($cleanedText);
        echo "Lowercase text: ";
        print_r($lower);

        $tokenizer = new WordTokenizer();
        $tokens = $tokenizer->tokenize($lower);

        $stopWords = new StopWords();
        $stopword = $stopWords->getStopWordsFromLanguage('fr');

        $cleanedTokens = array_diff($tokens, $stopword);
        echo "cleanedTokens : ";
        print_r($cleanedTokens);

        return $cleanedTokens;
    }

    private function transformTextToVectors(array $tokens): string
    {
        /**
        // Use TfIdfTransformer for feature extraction
        $tfIdfTransformer = new TfIdfTransformer($tokens);

        // Get the Tf-Idf matrix
        $tfIdfMatrix = $tfIdfTransformer->transform($tokens);

        // Convert Tf-Idf matrix to vectors
        $vectors = [];
        foreach ($tfIdfMatrix as $row) {
            $vectors[] = implode(',', $row);
        }

        return $vectors;
         * */
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
