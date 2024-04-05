<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

abstract class AbstractController extends Controller
{
    /**
     * Return JSON response for throwable.
     *
     * @param \Throwable $th
     * @return JsonResponse
     */
    protected function throwableResponse($th): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => $th->getMessage()
        ], 500);
    }
}