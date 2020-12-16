<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function testRequestShouldHaveEmailAndPassword()
    {
        $this
            ->postJson('/auth/token')
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                "email",
                "password"
            ]);
    }

    public function testCredentialsMustBeCorrect()
    {
        $user = User::factory()->create();

        $this
            ->postJson('/auth/token', [
                "email" => $user->email,
                "password" => "wrong password"
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                "email" => "The provided credentials are incorrect."
            ]);
    }

    public function testUserShouldGetHisOwnKey()
    {
        $user = User::factory()->create();

        $response = $this->postJson('/auth/token', [
            "email" => $user->email,
            "password" => "password"
        ]);

        $response->assertOk();

        [$id, $user_token] = explode("|", $response->original);

        $tokens = $user->tokens->map(fn ($token) => $token->token)->toArray();

        $this->assertContainsEquals(hash('sha256', $user_token), $tokens);
    }
}