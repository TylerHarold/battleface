<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

use App\Models\User;

class AuthController extends Controller
{
    /**
     * Create a new instance
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * Get a JWT via given credentials
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function login(Request $request): JsonResponse
    {
        // Create a validator with the input data
        $validator = Validator::make($request->all(), [
           'email' => 'required|email',
           'password' => 'required|string|min:8',
        ]);

        // If the input data fails, return the validator errors
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Attempt an auth request with the validated input data
        if (!$token = auth()->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->createNewToken($token);
    }

    /**
     * Registers a user
     *
     * @throws ValidationException
     *
     * @return JsonResponse;
     */
    public function register(Request $request): JsonResponse
    {
        // Create a validator with the input data
        $validator = Validator::make($request->all(), [
           'name' => 'required|string|between:2,100',
           'email' => 'required|string|email|max:100|unique:users',
           'password' => 'required|string|confirmed|min:8',
        ]);

        // If the input data fails, return the validator errors
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        // Create an entry in the users table
        $user = User::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)]
        ));

        // Return a JSON response
        return response()->json([
           'message' => 'User successfully registered',
           'user' => $user
        ], 201);
    }

    /**
     * Log out & invalidate the token
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        auth()->logout();

        return response()->json([
            'message' => 'User successfully signed out.'
        ]);
    }

    /**
     * Refresh a token
     *
     * @return JsonResponse
     */
    public function refresh(): JsonResponse
    {
        return $this->createNewToken(auth()->refresh());
    }

    /**
     * Get the authenticated user
     *
     * @return JsonResponse
     */
    public function user(): JsonResponse
    {
        return response()->json([auth()->user()]);
    }

    /**
     * Get the token array structure
     *
     * @param string $token
     *
     * @return JsonResponse
     */
    public function createNewToken($token): JsonResponse
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user(),
        ]);
    }
}
