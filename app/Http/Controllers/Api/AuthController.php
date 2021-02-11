<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Models\User;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

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
            /** @var User $user */
            $user = User::where('email', '=', $data['email'])->get()->first();

            return response()->json([
                'success' => true,
                'user' => User::where('email', '=', $data['email'])->get()->first(),
                'token' => $user->createToken('frontend')->plainTextToken,
                'token_type' => 'Bearer'
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

    public function redirectToProvider(string $provider)
    {


        return Socialite::driver($provider)->stateless()->redirect();
    }

    public function handleProviderCallback(string $provider)
    {
        $validated = $this->validateProvider($provider);
        if (!is_null($validated)) {
            return $validated;
        }

        try {
            $user = Socialite::driver($provider)->stateless()->user();
        } catch (ClientException $e) {
            return response()->json([
                'error' => 'Invalid credentials provided.'
            ], 422);
        }

        $userCreated = User::firstOrCreate([
            'email' => $user->getEmail()
        ], [
            'email_verified_at' => now(),
            'name' => $user->getName(),
            'status' => true
        ]);
        $userCreated->providers()->updateOrCreate(
            [
                'provider' => $provider,
                'provider_id' => $user->getId()
            ],
            [
                'avatar' => $user->getAvatar()
            ]
        );

        $token = $userCreated->createToken('socialite')->plainTextToken;

        return response()->json([
            'success' => true,
            'user' => $userCreated
        ], 200, ['Access-Token' => $token]);
    }

    private function validateProvider(string $provider)
    {
        return null;
        if (!in_array($provider, ['github'])) {
            return response()->json([
                'error' => 'Invalid provider.'
            ], 422);
        } else {
            return true;
        }
    }
}
