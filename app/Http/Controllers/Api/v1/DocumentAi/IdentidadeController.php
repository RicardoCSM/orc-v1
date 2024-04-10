<?php

namespace App\Http\Controllers\Api\v1\DocumentAi;

use App\Http\Requests\Api\v1\DocumentAi\DocumentRequest;
use App\Services\CpfService;
use App\Services\DateService;
use App\Services\LocalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

class IdentidadeController extends AbstractDocumentAiController
{
    /**
     * Process the document and return the identity information.
     *
     * @param  DocumentRequest  $request
     * @return JsonResponse
     */
    public function getIdentity(DocumentRequest $request)
    {
        try {
            $processorId = env('DOCUMENT_AI_IDENTIDADE_PROCESSOR_ID');
            $document = $this->processDocument($request, $processorId);
            $entities = $this->repeatedFieldToArray($document->getEntities());
            $identityData = $this->extractIdentity($entities);

            if ($identityData) {
                return response()->json([
                    'status' => true,
                    'result' => $identityData
                ], 200);
            } else {
                return $this->identityNotFoundedResponse();
            }
        } catch (\Throwable $th) {
            return $this->throwableResponse($th);
        }
    }

    /**
     * Extract identity information from entities.
     *
     * @param array $entities
     * @return Collection|null
     */
    private function extractIdentity(array $entities): Collection | null
    {
        $cpf = $this->getValueByType($entities, 'cpf');
        $rg = $this->getValueByType($entities, 'rg');

        if($cpf) {
            $cpfNumbers = CpfService::extractNumbers($cpf);
            if (strlen($cpfNumbers) == 11) {
                $cpf = CpfService::formatCpf($cpfNumbers);
            } else {
                $cpf = null;
            }
        }

        if (!$cpf || !$rg) {
            return null;
        }

        return collect([
            'nome' => $this->getValueByType($entities, 'nome'),
            'nome_mae' => $this->getValueByType($entities, 'nome-mae'),
            'nome_pai' => $this->getValueByType($entities, 'nome-pai'),
            'cpf' => $cpf,
            'rg' => $rg,
            'data_expedicao' => DateService::formatDate(($this->getValueByType($entities, 'data-expedicao'))),
            'data_nascimento' => DateService::formatDate($this->getValueByType($entities, 'data-nascimento')),
            'naturalidade' => LocalService::formatLocal($this->getValueByType($entities, 'naturalidade')),
        ]);
    }

    /**
     * Return the identity not founded response.
     *
     * @return JsonResponse
     */
    private function identityNotFoundedResponse(): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => 'Invalid, or insufficient, document sended!'
        ], 404);
    }
}
