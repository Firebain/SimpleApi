<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;

class AuthTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testRequestShouldHaveEmailAndPassword()
    {
        $response = $this->postJson('/auth/token');

        $response->assertJsonValidationErrors([
            "email",
            "password"
        ]);

        $response->assertStatus(422);
    }

    public function testCredentialsMustBeCorrect()
    {
        $user = User::factory()->create();

        $response = $this->postJson('/auth/token', [
            "email" => $user->email,
            "password" => "wrong password"
        ]);

        $response->assertJsonValidationErrors(["email" => "The provided credentials are incorrect."]);

        $response->assertStatus(422);
    }

    public function testUserShouldGetHisOwnKey()
    {
        $user = User::factory()->create();

        $response = $this->postJson('/auth/token', [
            "email" => $user->email,
            "password" => "password"
        ]);

        [$id, $user_token] = explode("|", $response->original);

        $tokens = $user->tokens->map(fn ($token) => $token->token)->toArray();

        $this->assertContainsEquals(hash('sha256', $user_token), $tokens);
    }
}