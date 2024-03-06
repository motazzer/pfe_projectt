<?php

namespace App\Message;


use App\Entity\CourseDocument;
use Doctrine\ORM\Mapping as ORM;

final class ExtractCourseDocumentContent
{
    private $courseDocumentId;

    public function __construct(int $courseDocumentId)
    {
        $this->courseDocumentId = $courseDocumentId;
    }
    public function getCourseDocumentId(): int
    {
        return $this->courseDocumentId;
    }
}
