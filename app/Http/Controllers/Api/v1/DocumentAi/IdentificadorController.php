<?php

namespace App\Http\Controllers\Api\v1\DocumentAi;

use App\Http\Requests\Api\v1\DocumentAi\DocumentRequest;
use Illuminate\Http\JsonResponse;

class IdentificadorController extends AbstractDocumentAiController
{
    /**
     * Process the document and return the type of the document.
     *
     * @param  DocumentRequest  $request
     * @return JsonResponse
     */
    public function getType(DocumentRequest $request)
    {
        try {
            $processorId = env('DOCUMENT_AI_IDENTIFICADOR_PROCESSOR_ID');
            $document = $this->processDocument($request, $processorId);
            $entities = $document->getEntities();

            $highestConfidence = 0.0;
            $identifiedType = null;

            foreach ($entities as $entity) {
                $confidence = $entity->getConfidence();
                if ($confidence > $highestConfidence) {
                    $highestConfidence = $confidence;
                    $identifiedType = $entity->getType();
                }
            }

            if (empty($identifiedType) || $highestConfidence < 0.5) {
                return $this->typeNotFoundedResponse();
            }

            return response()->json([
                'status' => true,
                'result' => $identifiedType
            ], 200);
        } catch (\Throwable $th) {
            return $this->throwableResponse($th);
        }
    }

    /**
     * Return the Type not founded response.
     *
     * @return JsonResponse
     */
    private function typeNotFoundedResponse(): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => 'Unable to identify a type in the document sent!'
        ], 404);
    }
}
