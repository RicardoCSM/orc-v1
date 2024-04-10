<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Api\v1\AbstractController;
use App\Http\Requests\Api\v1\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class UserController extends AbstractController
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Display the user information.
     * 
     * @param number $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        try {
            $user = $this->user->where('id', $id)->get();

            if($user->isEmpty()) {
                return $this->userNotFoundedResponse();
            }

            return response()->json([
                'status' => true,
                'result' => $user
            ], 200);
        } catch(\Throwable $th) {
            $this->throwableResponse($th);
        }
    }

    /**
     * Update the specified user information.
     * 
     * @param UpdateUserRequest $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(UpdateUserRequest $request, string $id): JsonResponse
    {
        try {
            $user = $this->user->where('id', $id);

            if($user->isEmpty()) {
                return $this->userNotFoundedResponse();
            }

            if ($request->old_password && !Hash::check($request->old_password, $user->password)) {
                return response()->json([
                    'status' => false,
                    'message' => "The specified password does not match the database password."
                ], 401);
            } else {
                $userData = $request->validated();

                if (isset($userData['password'])) {
                    $userData['password'] = Hash::make($userData['password']);
                }

                $user->fill($userData);
                $user->save();
                
                return response()->json([
                    'status' => true,
                    'result' => $user
                ], 200);
            }
        } catch(\Throwable $th) {
            $this->throwableResponse($th);
        }
    }

    /**
     * Delete the specified user.
     * 
     * @param number $id
     * @return void
     */
    public function destroy($id)
    {
        try {
            $user = $this->user->where('id', $id);

            if($user->isEmpty()) {
                return $this->userNotFoundedResponse();
            }
            
            $user->tokens()->delete();
            $user->delete();
        } catch(\Throwable $th) {
            $this->throwableResponse($th);
        }
    }

    /**
     * Return the User not founded response.
     *
     * @return JsonResponse
     */
    private function userNotFoundedResponse(): JsonResponse
    {
        return response()->json([
            'status' => false,
            'message' => 'User not found'
        ], 404);
    }
}