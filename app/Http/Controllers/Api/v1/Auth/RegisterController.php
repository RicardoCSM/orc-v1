<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Api\v1\AbstractController;
use App\Http\Requests\Api\v1\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class RegisterController extends AbstractController
{
    /**
     * Create User
     * @param RegisterRequest $request
     * @return JsonResponse 
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            return $this->registerResponse($user);
        } catch (\Throwable $th) {
            return $this->throwableResponse($th);
        }
    }

    /**
     * Return JSON response for successful register.
     *
     * @param  User  $user
     * @return JsonResponse
     */
    private function registerResponse($user): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => 'User Created Successfully',
            'token' => $user->createToken("API TOKEN")->plainTextToken
        ], 200);
    }
}
