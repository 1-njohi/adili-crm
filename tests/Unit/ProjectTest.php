<?php

namespace Tests\Unit;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    public function test_project_has_name(): void
    {
        $project = Project::factory()->create(['name' => 'Diani Silver Court']);

        $this->assertEquals('Diani Silver Court', $project->name);
    }

    public function test_project_has_plots(): void
    {
        $project = Project::factory()->create();
        $project->plots()->createMany([
            ['plot_number' => 1, 'size' => '0.25', 'size_unit' => 'acres'],
            ['plot_number' => 2, 'size' => '0.25', 'size_unit' => 'acres'],
        ]);

        $this->assertCount(2, $project->plots);
    }

    public function test_project_has_expenses(): void
    {
        $project = Project::factory()->create();
        $project->expenses()->create([
            'created_by' => 1,
            'amount' => 2000000,
            'currency' => 'KES',
            'category' => 'Land Purchase',
            'description' => 'Land purchase',
            'incurred_at' => now(),
            'type' => 'pre_launch',
        ]);

        $this->assertCount(1, $project->expenses);
    }

    public function test_project_has_status_helpers(): void
    {
        $draft = Project::factory()->create(['status' => 'draft']);
        $active = Project::factory()->create(['status' => 'active']);
        $soldOut = Project::factory()->create(['status' => 'sold_out']);

        $this->assertTrue($draft->isDraft());
        $this->assertTrue($active->isActive());
        $this->assertTrue($soldOut->isSoldOut());
    }
}
