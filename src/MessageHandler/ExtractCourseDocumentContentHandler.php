<?php

namespace App\MessageHandler;

use AllowDynamicProperties;
use App\Entity\CourseDocument;
use App\Message\ExtractCourseDocumentContent;
use Doctrine\Persistence\ManagerRegistry;
use Smalot\PdfParser\Parser;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use PhpOffice\PhpWord\IOFactory;
use DOMDocument;
use DOMXPath;


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
            return;
        }

        $filePath = $courseDocument->getFilePath();
        $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);

        switch ($fileExtension) {
            case 'pdf':
                $content = $this->extractPdfContent($filePath);
                break;
            case 'docx':
                $content = $this->extractDocxContent($filePath);
                break;
            case 'txt':
                $content = $this->extractTxtContent($filePath);
                break;
            case 'html':
                $content = $this->extractHtmlContent($filePath);
                break;
            default:
                $content = '';
                break;
        }
        $courseDocument->setContent($content);
        $entityManager->flush();
    }

    private function extractPdfContent(string $filePath): string
    {
        $parser = new Parser();
        $pdf = $parser->parseFile($filePath);
        $fileContent = '';
        foreach ($pdf->getPages() as $page) {
            $fileContent .= $page->getText();
        }
        return $fileContent;
    }

    private function extractDocxContent(string $filePath): string
    {
        $phpWord = IOFactory::load($filePath);
        $content = '';
        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if ($element instanceof \PhpOffice\PhpWord\Element\TextRun) {
                    $content .= $element->getText();
                }
            }
        }
        return $content;
    }

    private function extractTxtContent(string $filePath): string
    {
        return file_get_contents($filePath);
    }
    private function extractHtmlContent(string $filePath): string
    {
        $htmlContent = file_get_contents($filePath);

        $dom = new DOMDocument();
        @$dom->loadHTML($htmlContent);

        $xpath = new DOMXPath($dom);
        $textContent = '';
        foreach ($xpath->query('//text()') as $node) {
            $textContent .= $node->nodeValue;
        }

        return $textContent;
    }
}
