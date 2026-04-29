<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Inscription d'un nouveau vendeur.
     *
     * POST /api/v1/auth/register
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create($request->validated()); // le mot de passe est automatiquement hashe dans le model User grace au mutateur setPasswordAttribute

        $token = $user->createToken('auth-token')->plainTextToken; // on genere un token d'authentification pour le nouvel utilisateur

        // On retourne les infos du user et le token d'auth dans la reponse
        return response()->json([
            'user'  => UserResource::make($user), // on utilise une ressource pour formater la reponse de l'utilisateur (ex : cacher le password, formater les dates, etc)
            'token' => $token, // on retourne le token d'authentification pour que le client puisse l'utiliser dans les requetes suivantes
        ], 201);
    }

    /**
     * Connexion d'un vendeur existant.
     *
     * POST /api/v1/auth/login
     */
    public function login(LoginRequest $request)
    {
        try {
            $credentials = $request->validated();

            if (! Auth::attempt($credentials)) {
                return response()->json(['message' => 'Identifiants incorrects.'], 401);
            }

            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'user' => new UserResource($user),
                'token' => $token,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ], 500);
        }
    }
    /**
     * Déconnexion : révoque le token utilisé pour la requête.
     *
     * POST /api/v1/auth/logout
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(null, 204);
    }
}
