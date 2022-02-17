<?php

namespace Tests\Feature;

use App\Enums\LoanStatus;
use App\Enums\LoanType;
use App\Models\Loan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoanTest extends TestCase
{
    use RefreshDatabase;
    private User $user;
    private User $admin;
    private Loan $loan;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['role' => 'user']);
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->loan = Loan::factory()->create(['user_id' => $this->user->id]);
    }

    /**
     * Check if user is able to apply for the loan.
     *
     * @return void
     */
    public function test_user_is_able_to_apply_for_loan()
    {
        $user = $this->user;

        $response = $this->post('/api/loan/apply', [
            'principal_amount' => 10000,
            'terms' => 10,
            'type' => LoanType::Home->value
        ],[
            'Authorization' => 'Bearer '.$user->createToken('test_token')->plainTextToken,
            'Accept' => 'application/json'
        ]);

        $response->assertStatus(200);
    }

    public function test_admin_is_able_to_approve_the_loan() {

        $admin = $this->admin;

        $response = $this->post("/api/loan/process/{$this->loan->id}/approved", [
            'interest_rate' => random_int(7,16),
        ],[
            'Authorization' => 'Bearer '.$admin->createToken('test_token')->plainTextToken,
            'Accept' => 'application/json'
        ]);

        $response->assertStatus(200);
    }

    public function test_admin_is_able_to_reject_the_loan() {

        $admin = $this->admin;

        $response = $this->post("/api/loan/process/{$this->loan->id}/rejected", [], [
            'Authorization' => 'Bearer '.$admin->createToken('test_token')->plainTextToken,
            'Accept' => 'application/json'
        ]);

        $response->assertStatus(200);
    }

    public function test_admin_is_not_able_process_already_processed_loan() {

        $admin = $this->admin;

        $loan = Loan::factory()->create(['user_id' => $this->user->id, 'status' => LoanStatus::Rejected->value]);

        $response = $this->post("/api/loan/process/{$loan->id}/approved", [], [
            'Authorization' => 'Bearer '.$admin->createToken('test_token')->plainTextToken,
            'Accept' => 'application/json'
        ]);

        $response->assertStatus(422);
    }

    public function test_user_is_not_able_to_approve_the_loan() {

        $user = $this->user;

        $response = $this->post("/api/loan/process/{$this->loan->id}/approved", [
            'interest_rate' => random_int(7,16),
        ],[
            'Authorization' => 'Bearer '.$user->createToken('test_token')->plainTextToken,
            'Accept' => 'application/json'
        ]);

        $response->assertStatus(403);
    }

    public function test_user_is_able_to_make_repayments() {

        $user = $this->user;
        $admin = $this->admin;

        $interest_amount = $this->loan->principal_amount * (7.5 / 100) * ($this->loan->terms / 52);

        $loan = Loan::factory()->create(['user_id' => $this->user->id, 'interest_rate' => 7.5, 'interest_amount' => $interest_amount, 'status' => LoanStatus::Approved->value]);

        $amount = $this->loan->remaining;
        $remaining_terms = $this->loan->terms - $this->loan->repayments()->count();
        $payable_amount = $amount / $remaining_terms;

        $response = $this->post("/api/loan/repay/{$loan->id}", [
            'payment_date' => Carbon::today(),
            'amount' => $payable_amount
        ],[
            'Authorization' => 'Bearer '.$user->createToken('test_token')->plainTextToken,
            'Accept' => 'application/json'
        ]);

        $response->assertStatus(200);
    }
}
