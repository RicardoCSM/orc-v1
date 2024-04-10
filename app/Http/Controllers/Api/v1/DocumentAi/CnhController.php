<?php

namespace App\Http\Controllers\Api\v1\DocumentAi;

use App\Http\Requests\Api\v1\DocumentAi\DocumentRequest;
use App\Services\CpfService;
use App\Services\DateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

class CnhController extends AbstractDocumentAiController
{
    /**
     * Process the document and return the cnh information.
     *
     * @param  DocumentRequest  $request
     * @return JsonResponse
     */
    public function getCnh(DocumentRequest $request)
    {
        try {
            $processorId = env('DOCUMENT_AI_CNH_PROCESSOR_ID');
            $document = $this->processDocument($request, $processorId);
            $entities = $this->repeatedFieldToArray($document->getEntities());
            $cnhData = $this->extractCnh($entities);

            if ($cnhData) {
                return response()->json([
                    'status' => true,
                    'result' => $cnhData
                ], 200);
            } else {
                return $this->cnhNotFoundedResponse();
            }
        } catch (\Throwable $th) {
            return $this->throwableResponse($th);
        }
    }

    /**
     * Extract cnh information from entities.
     *
     * @param array $entities
     * @return Collection|null
     */
    private function extractCnh(array $entities): Collection | null
    {
        $cpf = $this->getValueByType($entities, 'cpf');
        $categoriaHabilitacao = $this->getValueByType($entities, 'categoria-habilitacao');

        if($cpf) {
            $cpfNumbers = CpfService::extractNumbers($cpf);
            if (strlen($cpfNumbers) == 11) {
                $cpf = CpfService::formatCpf($cpfNumbers);
            } else {
                $cpf = null;
            }
        }

        if (!$cpf || !$categoriaHabilitacao) {
            return null;
        }

        return collect([
            'nome' => $this->getValueByType($entities, 'nome'),
            'doc_identidade' => $this->getValueByType($entities, 'doc-identidade'),
            'org-emissor' => $this->getValueByType($entities, 'org-emissor'),
            'uf' => $this->getValueByType($entities, 'uf'),
            'validade' => $this->getValueByType($entities, 'validade'),
            'cpf' => $cpf,
            'categoria_habilitacao' => $categoriaHabilitacao,
            'data_emissao' =>  DateService::formatDate($this->getValueByType($entities, 'data-emissao')),
            'nome_mae' => $this->getValueByType($entities, 'nome-mae'),
            'nome_pai' => $this->getValueByType($entities, 'nome-pai'),
            'data_nascimento' => DateService::formatDate($this->getValueByType($entities, 'data-nascimento')),
            'observacoes ' => $this->getValueByType($entities, 'observacoes'),
            'local ' => $this->getValueByType($entities, 'local'),
            'num_registro' => $this->getValueByType($entities, 'num-registro'),
        ]);
    }

    /**
     * Return the cnh not founded response.
     *
     * @return JsonResponse
     */
    private function cnhNotFoundedResponse(): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => 'Invalid, or insufficient, document sended!'
        ], 404);
    }
}
