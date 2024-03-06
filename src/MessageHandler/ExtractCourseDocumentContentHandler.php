<?php

namespace App\MessageHandler;

use AllowDynamicProperties;
use App\Entity\CourseDocument;
use App\Message\ExtractCourseDocumentContent;
use Doctrine\Persistence\ManagerRegistry;
use Smalot\PdfParser\Parser;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

#[AllowDynamicProperties]
final class ExtractCourseDocumentContentHandler implements MessageHandlerInterface
{
    private $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    public function __invoke(ExtractCourseDocumentContent $message): void
    {
        $entityManager = $this->managerRegistry->getManager();
        $courseDocument = $entityManager->getRepository(CourseDocument::class)->find($message->getCourseDocumentId());
        if (!$courseDocument) {
            return ;
        }
        $parser = new Parser();
        $pdf = $parser->parseFile($courseDocument->getFilePath());
        $fileContent = '';
        foreach ($pdf->getPages() as $page) {
            $fileContent .= $page->getText();
        }
        $courseDocument->setContent($fileContent);
        $entityManager->flush();
    }
}
