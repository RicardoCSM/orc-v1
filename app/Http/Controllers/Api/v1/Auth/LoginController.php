<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Api\v1\AbstractController;
use App\Http\Requests\Api\v1\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class LoginController extends AbstractController
{
    /**
     * Authenticate The User
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $request->authenticate();

            $user = User::where('email', $request->email)->first();

            $token = $user->createToken("API TOKEN")->plainTextToken;

            return $this->loginResponse($token);

        } catch (\Throwable $th) {
            return $this->throwableResponse($th);
        }
    }

    /**
     * Return JSON response for successful login.
     *
     * @param  string  $token
     * @return JsonResponse
     */
    private function loginResponse($token): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => 'User authenticated successfully!',
            'token' => $token
        ], 200);
    }
}