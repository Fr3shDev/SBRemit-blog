<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\RegisterUserRequest;
use App\Http\Resources\UserResource;
use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function __construct(
        private UserRepositoryInterface $userRepository,

    ) {}

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
}
