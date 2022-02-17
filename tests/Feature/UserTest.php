<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_user_is_able_to_login()
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->post('/api/login',[
            'email' => $user->email,
            'password' => 'password'
        ]);

        $response->assertStatus(200);
    }
}
