<?php

namespace Tests\Unit;

use App\Models\Plot;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlotTest extends TestCase
{
    use RefreshDatabase;

    public function test_plot_belongs_to_project(): void
    {
        $project = Project::factory()->create();
        $plot = Plot::factory()->create(['project_id' => $project->id]);

        $this->assertEquals($project->id, $plot->project_id);
        $this->assertInstanceOf(Project::class, $plot->project);
    }

    public function test_plot_has_payment_tiers(): void
    {
        $plot = Plot::factory()->create([
            'payment_tiers' => [
                ['tier' => '1-3 months', 'price' => 1000000],
                ['tier' => '4-6 months', 'price' => 1050000],
            ],
        ]);

        $this->assertIsArray($plot->payment_tiers);
        $this->assertCount(2, $plot->payment_tiers);
    }

    public function test_plot_has_custom_attributes(): void
    {
        $plot = Plot::factory()->create([
            'custom_attributes' => [
                'road_access' => true,
                'view' => 'ocean',
            ],
        ]);

        $this->assertIsArray($plot->custom_attributes);
        $this->assertTrue($plot->custom_attributes['road_access']);
        $this->assertEquals('ocean', $plot->custom_attributes['view']);
    }

    public function test_plot_has_status_helpers(): void
    {
        $available = Plot::factory()->create(['status' => 'available']);
        $reserved = Plot::factory()->create(['status' => 'reserved']);
        $sold = Plot::factory()->create(['status' => 'sold']);

        $this->assertTrue($available->isAvailable());
        $this->assertTrue($reserved->isReserved());
        $this->assertTrue($sold->isSold());
    }

    public function test_plot_can_get_price_for_tier(): void
    {
        $plot = Plot::factory()->create([
            'payment_tiers' => [
                ['tier' => '1-3 months', 'price' => 1000000],
                ['tier' => '4-6 months', 'price' => 1050000],
            ],
        ]);

        $this->assertEquals(1000000, $plot->getPriceForTier('1-3 months'));
        $this->assertEquals(1050000, $plot->getPriceForTier('4-6 months'));
        $this->assertNull($plot->getPriceForTier('7-12 months'));
    }
}
