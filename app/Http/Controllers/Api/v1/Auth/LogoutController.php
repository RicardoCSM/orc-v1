<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Api\v1\AbstractController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LogoutController extends AbstractController
{
    /**
     * Logout the user and revoke the token.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            $user->tokens()->delete();

            return $this->logoutResponse();
        } catch (\Throwable $th) {
            return $this->throwableResponse($th);
        }
    }

    /**
     * Return JSON response for successful logout.
     *
     * @return JsonResponse
     */
    private function logoutResponse(): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => 'User logged out successfully.'
        ], 200);
    }
}
