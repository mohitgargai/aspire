<?php

namespace Database\Factories;

use App\Models\LoanRepayment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LoanRepayment>
 */
class LoanRepaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'loan_id' => 1,
            'amount' => 8000,
            'due_date' => Carbon::now()->addDays(7)
        ];
    }

}
