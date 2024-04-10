<?php

namespace App\Http\Controllers\Api\v1\DocumentAi;

use App\Http\Requests\Api\v1\DocumentAi\DocumentRequest;
use App\Services\CpfService;
use Illuminate\Http\JsonResponse;

class CpfController extends AbstractDocumentAiController
{
    /**
     * Process the document and return the CPF.
     *
     * @param  DocumentRequest  $request
     * @return JsonResponse
     */
    public function getCpf(DocumentRequest $request)
    {
        try {
            $processorId = env('DOCUMENT_AI_CPF_PROCESSOR_ID');

            $document = $this->processDocument($request, $processorId);
            $entities = $this->repeatedFieldToArray($document->getEntities());
            
            $cpf = $this->getValueByType($entities, 'cpf');
            $cpf = CpfService::extractNumbers($cpf);

            if ($cpf === null || strlen($cpf) != 11) {
                return $this->cpfNotFoundedResponse();
            }

            $cpf = CpfService::formatCpf($cpf);

            return response()->json([
                'status' => true,
                'result' => $cpf
            ], 200);
        } catch (\Throwable $th) {
            return $this->throwableResponse($th);
        }
    }

    /**
     * Return the CPF not founded response.
     *
     * @return JsonResponse
     */
    private function cpfNotFoundedResponse(): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => 'CPF not founded in the file sended!'
        ], 404);
    }
}
