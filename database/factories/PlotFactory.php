<?php

namespace Database\Factories;

use App\Models\Plot;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlotFactory extends Factory
{
    protected $model = Plot::class;

    public function definition(): array
    {
        static $plotNumber = 0;
        $plotNumber++;

        return [
            'project_id' => Project::factory(),
            'plot_number' => $plotNumber,
            'size' => '0.25',
            'size_unit' => 'acres',
            'payment_tiers' => [
                ['tier' => '1-3 months', 'price' => 1000000],
                ['tier' => '4-6 months', 'price' => 1050000],
                ['tier' => '7-12 months', 'price' => 1100000],
            ],
            'custom_attributes' => [
                'road_access' => $this->faker->boolean(80),
                'view' => $this->faker->randomElement(['ocean', 'garden', 'street', 'none']),
            ],
            'status' => 'available',
        ];
    }

    public function reserved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'reserved',
        ]);
    }

    public function sold(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'sold',
        ]);
    }
}
