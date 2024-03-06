<?php

namespace App\Services;

use Hasanmertermis\MilvusPhpClient\Domain\Helpers;
use Hasanmertermis\MilvusPhpClient\Domain\Milvus;
use Hasanmertermis\MilvusPhpClient\Domain\Schema\Field;
use Milvus\Proto\Schema\DataType;


require_once __DIR__ . '/../../vendor/autoload.php';

class MilvusService
{
    private $client;
    public function __construct()
    {
        $this->client = new Milvus();
        $this->client->connection("localhost", 19530);
    }

    public function insertVector(string $collectionName, array $vector, array $additionalData = []): void
    {
        try {
            $fields = [
                (new Field())->setFieldName('encoding')
                    ->setFieldData([$vector])
                    ->setFieldType(DataType::FloatVector),
            ];

            foreach ($additionalData as $fieldName => $data) {
                $fields[] = (new Field())
                    ->setFieldName($fieldName)
                    ->setFieldData($data)
                    ->setFieldType(DataType::FloatVector);
            }
            $this->client->insert($fields, $collectionName);
        } catch (\Exception $e) {
            throw new \RuntimeException("Error inserting vector into Milvus: {$e->getMessage()}");
        }
    }

}