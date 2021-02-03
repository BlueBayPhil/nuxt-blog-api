<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserAuthenticationTest extends TestCase
{
    use WithFaker;

    public function test_users_can_login()
    {
        $this->withoutExceptionHandling();
        $password = $this->faker->password;
        $user = User::create([
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => Hash::make($password)
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => $password
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'user',
            ]);
    }

    public function test_users_can_register_an_account()
    {
        $this->withoutExceptionHandling();

        $data = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => $this->faker->password
        ];

        $response = $this->postJson('/api/register', $data);
        $response->assertStatus(201)->assertJson([
            'success' => true
        ]);
    }
}
