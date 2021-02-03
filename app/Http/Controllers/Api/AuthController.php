<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * @param LoginUserRequest $request
     * @return JsonResponse
     */
    public function login(LoginUserRequest $request): JsonResponse
    {
        $data = $request->validated();

        if (Auth::attempt($data)) {
            return response()->json([
                'success' => true,
                'user' => User::where('email', '=', $data['email'])->get()->first()
            ], Response::HTTP_OK);
        }

        return response()->json([
            'success' => false,
            'error' => 'Invalid Credentials Provided.'
        ], Response::HTTP_FORBIDDEN);
    }

    /**
     * @param RegisterUserRequest $request
     * @return JsonResponse
     */
    public function register(RegisterUserRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = User::create($data);

        return response()->json([
            'success' => true
        ], Response::HTTP_CREATED);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        /**
         * @var User $user
         */
        $user = $request->user();
        $user->tokens()->where('id', $user->currentAccessToken()->id)->delete();

        return response()->json([
            'success' => true
        ], Response::HTTP_OK);
    }
}
