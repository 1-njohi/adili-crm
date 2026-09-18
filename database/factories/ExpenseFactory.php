<?php

namespace Database\Factories;

use App\Models\Expense;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExpenseFactory extends Factory
{
    protected $model = Expense::class;

    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'created_by' => User::factory()->superadmin(),
            'amount' => $this->faker->numberBetween(10000, 500000),
            'currency' => 'KES',
            'category' => $this->faker->randomElement([
                'Land Purchase', 'Survey', 'Roads', 'Water', 'Legal',
                'Marketing', 'Logistics', 'Client Entertainment', 'Miscellaneous',
            ]),
            'description' => $this->faker->sentence(),
            'incurred_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'receipt_path' => null,
            'internal_notes' => $this->faker->optional()->sentence(),
            'type' => $this->faker->randomElement(['pre_launch', 'post_launch']),
        ];
    }

    public function preLaunch(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'pre_launch',
        ]);
    }

    public function postLaunch(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'post_launch',
        ]);
    }
}
