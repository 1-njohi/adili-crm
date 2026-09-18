<?php

namespace Database\Seeders;

use App\Models\Expense;
use App\Models\Plot;
use App\Models\Project;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(SuperadminSeeder::class);

        // Create a sample project with plots and expenses
        $project = Project::factory()->create([
            'name' => 'Diani Silver Court',
            'location' => 'Diani Beach, Kenya',
            'land_size' => '2',
            'status' => 'active',
            'neighbor_discount' => ['percentage' => 5, 'active' => true],
        ]);

        // Create 8 plots
        for ($i = 1; $i <= 8; $i++) {
            Plot::factory()->create([
                'project_id' => $project->id,
                'plot_number' => $i,
                'size' => '0.25',
                'status' => $i <= 5 ? 'available' : 'sold',
            ]);
        }

        // Create some expenses
        Expense::factory()->create([
            'project_id' => $project->id,
            'category' => 'Land Purchase',
            'amount' => 2000000,
            'type' => 'pre_launch',
            'description' => 'Purchase of 2-acre track in Diani',
        ]);

        Expense::factory()->create([
            'project_id' => $project->id,
            'category' => 'Survey',
            'amount' => 300000,
            'type' => 'pre_launch',
            'description' => 'Boundary survey and beacon planting',
        ]);

        Expense::factory()->create([
            'project_id' => $project->id,
            'category' => 'Roads',
            'amount' => 500000,
            'type' => 'pre_launch',
            'description' => 'Murram road grading',
        ]);

        Expense::factory()->create([
            'project_id' => $project->id,
            'category' => 'Water',
            'amount' => 400000,
            'type' => 'pre_launch',
            'description' => 'Borehole drilling',
        ]);
    }
}
