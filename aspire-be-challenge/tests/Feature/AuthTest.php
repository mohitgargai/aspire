<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{

    use RefreshDatabase;

    public function test_required_fields_for_registration()
    {
        $this->json('POST', 'api/register', ['Accept' => 'application/json'])
            ->assertStatus(422)
            ->assertJson([
                "errors" => [
                    "name" => ["The name field is required."],
                    "email" => ["The email field is required."],
                    "password" => ["The password field is required."],
                ]
            ]);
    }

    public function test_successful_registration()
    {
        $userData = [
            "name" => "Mohit Garg",
            "email" => "mohit.aspire@gmail.com",
            "password" => "123456",
        ];

        $this->json('POST', 'api/register', $userData, ['Accept' => 'application/json'])
            ->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'user_id',
                ],
            ]);
    }

    public function test_must_enter_email_and_password()
    {
        $this->json('POST', 'api/login')
            ->assertStatus(422)
            ->assertJson([
                "errors" => [
                    'email' => ["The email field is required."],
                    'password' => ["The password field is required."],
                ]
            ]);
    }

    public function test_successful_login()
    {
        $user = User::factory()->create();

        $loginData = ['email' => $user->email, 'password' => 'password'];

        $this->json('POST', 'api/login', $loginData, ['Accept' => 'application/json'])
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'token',
                    'user_id',
                ],
            ]);

        $this->assertAuthenticated();
    }

    public function test_unsuccessful_login()
    {
        $user = User::factory()->create();

        $loginData = ['email' => $user->email, 'password' => '123456'];

        $this->json('POST', 'api/login', $loginData, ['Accept' => 'application/json']);

        $this->assertGuest();
    }
}
