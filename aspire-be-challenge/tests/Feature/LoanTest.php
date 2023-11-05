<?php

namespace Tests\Feature;

use App\Models\Loan;
use App\Models\LoanRepayment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoanTest extends TestCase
{

    use RefreshDatabase;

    public function test_required_fields_for_creating_loan_request()
    {
        $user = User::factory()->create();

        $this->actingAs($user)->json('POST', 'api/create-loan-request/', ['Accept' => 'application/json'])
            ->assertStatus(422)
            ->assertJson([
                "errors" => [
                    "amount" => ["The amount field is required."],
                    "term" => ["The term field is required."],
                ]
            ]);
    }

    public function test_creating_loan_request()
    {
        $user = User::factory()->create();

        $data = [
            "amount" => 8000,
            "term" => 3,
        ];

        $this->actingAs($user)->json('POST', 'api/create-loan-request/', $data, ['Accept' => 'application/json'])
            ->assertStatus(201)
            ->assertJson([
                "data" => [
                    "loan_id" => 1,
                ]
            ]);
    }

    public function test_required_fields_for_adding_loan_repayment()
    {
        $user = User::factory()->create();

        $this->actingAs($user)->json('POST', 'api/add-loan-repayment/', ['Accept' => 'application/json'])
            ->assertStatus(422)
            ->assertJson([
                "errors" => [
                    "loan_id" => ["The loan id field is required."],
                    "amount" => ["The amount field is required."],
                ]
            ]);
    }

    public function test_adding_loan_repayment()
    {
        $user = User::factory()->create();
        $loan = Loan::factory()->create([
            "user_id" => $user->id,
            "amount" => 8000,
            "term" => 3,
        ]);
        $loanRepayment = LoanRepayment::factory()->create([
            "loan_id" => $loan->id,
            "amount" => 2666.67,
        ]);

        $data = [
            "loan_id" => $loan->id,
            "amount" => 2666.67,
        ];

        $this->actingAs($user)->json('POST', 'api/add-loan-repayment/', $data, ['Accept' => 'application/json'])
            ->assertStatus(200)
            ->assertJson([
                "data" => [
                    "loan_repayment_id" => $loanRepayment->id,
                ]
            ]);
    }

}
