<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Requests\API\LoginUserRequest;
use App\Http\Requests\API\RegisterUserRequest;
use App\Interfaces\UserRepositoryInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

// test('register creates a new user successfully', function () {
//     $userRepo = $this->mock(UserRepositoryInterface::class);
//     $userRepo->shouldReceive('createUser')->once()->andReturn(User::factory()->make());

//     $request = RegisterUserRequest::create('/register', 'POST', [
//         'name' => 'Test User',
//         'email' => 'test@example.com',
//         'phone' => '1234567890',
//         'password' => 'password',
//         'password_confirmation' => 'password',
//     ]);

//     $response = app(AuthController::class)->register($request);

//     $response->assertStatus(200)
//         ->assertJson([
//             'message' => 'Account created successfully',
//             'attributes' => [
//                 'name' => 'Test User',
//                 'email' => 'test@example.com',
//             ]
//         ]);
// });

test('registers a new user successfully', function () {
    // Prepare the db
    $user = User::factory()->create([
        'name' => 'Test user',
        'email' => 'test@example.com',
        'phone' => '1234567890',
        'password' => bcrypt('password'),
    ]);
    $this->actingAs($user);
    $response = $this->post('/api/auth/register');
    $response->assertStatus(302);
});

test('register fails if user already exists', function () {
    $response = $this->post('/api/auth/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'phone' => '1234567890',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);


    $response->assertStatus(400)
        ->assertJson([
            'message' => 'User already exists',
        ]);
});


test('login with correct credentials', function () {
    $response = $this->post('/api/auth/login', [
        'email' => 'test@example.com',
        'password' => 'password',
    ]);

    $response->assertStatus(200);
});

test('login fails with incorrect credentials', function () {
   $response = $this->post('/api/auth/login', [
    'email' => 'test@example.com',
    'password' => 'wrongpassword',
   ]);

    $response->assertStatus(401)
        ->assertJson([
            'message' => 'Invalid email or password',
        ]);
});

test('logout successfully', function () {
    // Create a user with known credentials
    $user = User::factory()->create([
        'email' => 'test9@example.com',
        'phone' => '1234567899',
        'password' => bcrypt('password'),
    ]);

    // Log in with the user to get a token or session
    $this->post('/api/auth/login', [
        'email' => 'test9@example.com',
        'password' => 'password',
    ]);

    // Act as the authenticated user
    $this->actingAs($user);

    // Send the logout request
    $response = $this->get('/api/auth/logout');

    // Assert that the response is successful and contains the expected JSON structure
    $response->assertStatus(200)
        ->assertJson([
            'status' => true,
            'message' => 'Logged out successfully',
        ]);
});
