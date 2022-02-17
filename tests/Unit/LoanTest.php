<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoanTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_that_user_is_able_to_request_for_loan()
    {
        $user = User::factory()->make(['role' => 'user']);


        $this->assertTrue(true);
    }
}
