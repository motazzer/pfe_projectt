<?php

namespace App\Message;

final class TransformTextToVectors
{
    private int $documentId;

    public function __construct(int $documentId)
    {
        $this->documentId = $documentId;
    }

    public function getDocumentId(): int
    {
        return $this->documentId;
    }

}
