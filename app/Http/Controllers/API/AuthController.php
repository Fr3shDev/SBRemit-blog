<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\LoginUserRequest;
use App\Http\Requests\API\RegisterUserRequest;
use App\Http\Resources\UserResource;
use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

/**
 * @tags User Authentication
 */
class AuthController extends Controller
{
    public function __construct(
        private UserRepositoryInterface $userRepository,

    ) {}

    /**
     * User Registration
     *
     * Registering new users in the system
     *
     * @unauthenticated
     */
    public function register(RegisterUserRequest $request)
    {
        $validated = $request->validated();

        // Check if the user already exists
        $user = User::where(function ($query) use ($validated) {
            $query->where('email', $validated['email'])->orWhere('phone', $validated['phone']);
        })->first();

        if ($user) {
            return response()->json([
                'message' => 'User already exists',
            ], 400);
        } else {
            DB::beginTransaction();
            try {
                // If no user is found, create a new user
                $userDetails = [
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'phone' => $validated['phone'],
                    'password' => $validated['password'],
                    'email_verified_at' => now(),
                ];
                $user = $this->userRepository->createUser($userDetails);

                $token = $user->createToken($user->name.'AuthToken')->plainTextToken;

                DB::commit();

                return new UserResource($user, $token, 'Bearer', 'Account created successfully');
            } catch (\Exception $e) {
                DB::rollBack();

                Log::error('User registration failed: '.$e->getMessage());

                return response()->json([
                    'message' => 'Account creation failed, please try again later',
                ], 500);
            }
        }
    }

    /**
     * User Login
     *
     * Authorizing users to have access
     *
     * @unauthenticated
     */
    public function login(LoginUserRequest $request)
    {
        $validated = $request->validated();
        $user = User::where('email', $validated['email'])->first();
        if ($user && Hash::check($validated['password'], $user->password)) {
            $token = $user->createToken($user->name.'AuthToken')->plainTextToken;

            return new UserResource($user, $token, 'Bearer', 'Login successful');
        } else {
            return response()->json([
                'message' => 'Invalid email or password',
            ], 401);
        }
    }

    /**
     * User Logout
     *
     * This endpoint allows the user to log out by invalidating the current authentication token.
     *
     * @authenticated
     */
    public function logout(Request $request)
    {
        $user = $request->user();
        if ($user) {
            $user->tokens()->delete();

            return response()->json(['status' => 'true', 'message' => 'Logged out successfully'], 200);
        }

        return response()->json(['status' => 'failed', 'message' => 'No user found to logout'], 404);
    }
}
