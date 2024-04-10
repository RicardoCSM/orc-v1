<?php

namespace App\Http\Controllers\Api\v1\DocumentAi;

use App\Http\Controllers\Api\v1\AbstractController;
use App\Http\Requests\Api\v1\DocumentAi\DocumentRequest;
use Google\Cloud\DocumentAI\V1\Client\DocumentProcessorServiceClient;
use Google\Cloud\DocumentAI\V1\Document;
use Google\Cloud\DocumentAI\V1\ProcessRequest;
use Google\Cloud\DocumentAI\V1\RawDocument;
use Google\Protobuf\Internal\RepeatedField;
use Illuminate\Http\JsonResponse;

abstract class AbstractDocumentAiController extends AbstractController
{
    /**
     * Process the file and return document.
     *
     * @param  DocumentRequest  $request
     * @param  string  $processorId
     * @return \Google\Cloud\DocumentAI\V1\Document|JsonResponse $document
     */
    protected function processDocument(DocumentRequest $request, string $processorId): Document | JsonResponse
    {
        try {
            $projectId = env('DOCUMENT_AI_PROJECT_ID');
            $location = env('DOCUMENT_AI_LOCATION');

            $name = "projects/{$projectId}/locations/{$location}/processors/{$processorId}";
            putenv('GOOGLE_APPLICATION_CREDENTIALS=' . base_path() . '/' . env('GOOGLE_APPLICATION_CREDENTIALS'));

            $document = $request->file('document');
            $encodedFile = file_get_contents($document->getPathname());
            $fileType = $document->getMimeType();
            $client = new DocumentProcessorServiceClient();

            $rawDocument = new RawDocument();
            $rawDocument->setContent($encodedFile);
            $rawDocument->setMimeType($fileType);

            $testRequest = new ProcessRequest([
                'name' => $name,
                'skip_human_review' => true,
                'raw_document' => $rawDocument
            ]);

            $response = $client->processDocument($testRequest);
            $document = $response->getDocument();
            return $document;
        } catch (\Throwable $th) {
            return $this->throwableResponse($th);
        }
    }

    /**
     * Return the RepeatedField as a array.
     *
     * @param RepeatedField $repeatedField
     * @return array
     */
    protected function repeatedFieldToArray(RepeatedField $repeatedField): array
    {
        $array = [];
        foreach ($repeatedField as $item) {
            $array[] = $item;
        }
        return $array;
    }

    /**
     * Get the value for a specific entity type from the entities array.
     *
     * @param array $entities
     * @param string $type
     * @return string|null
     */
    protected function getValueByType(array $entities, string $type): ?string
    {
        foreach ($entities as $entity) {
            if ($entity->getType() === $type) {
                if (!empty($entity)) {
                    return $entity->getMentionText();
                } else {
                    return null;
                }
            }
        }

        return null;
    }
}
