<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company().' Estate',
            'location' => $this->faker->city().', Diani',
            'land_size' => '2',
            'land_size_unit' => 'acres',
            'status' => 'draft',
            'neighbor_discount' => ['percentage' => 5, 'active' => false],
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    public function soldOut(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'sold_out',
        ]);
    }
}
