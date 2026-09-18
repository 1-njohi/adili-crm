<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\LeadTether;
use App\Models\SiteVisit;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $agentId = auth()->id();

        $totalLeads = LeadTether::where('agent_id', $agentId)->count();
        \Log::info($totalLeads);
        $activeLeads = LeadTether::where('agent_id', $agentId)
            ->where('expires_at', '>', now())
            ->count();
        $expiredLeads = LeadTether::where('agent_id', $agentId)
            ->where('expires_at', '<=', now())
            ->count();

        $recentLeads = LeadTether::where('agent_id', $agentId)
            ->with('project')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $upcomingVisits = SiteVisit::where('booked_by_id', $agentId)
            ->where('status', '!=', 'completed')
            ->where('scheduled_at', '>', now())
            ->orderBy('scheduled_at', 'asc')
            ->limit(5)
            ->get();

        $totalVisits = SiteVisit::where('booked_by_id', $agentId)->count();

        $sourceStats = LeadTether::where('agent_id', $agentId)
            ->select('source_platform', \DB::raw('count(*) as total'))
            ->groupBy('source_platform')
            ->get();

        // Ensure this is always an array
        return Inertia::render('agent/Dashboard', [
            'stats' => [
                'total_leads' => $totalLeads ?? 0,
                'active_leads' => $activeLeads ?? 0,
                'expired_leads' => $expiredLeads ?? 0,
                'total_visits' => $totalVisits ?? 0,
                'total_commission' => 0, // You'll add this later
                'pending_commission' => 0,
            ],
            'sourceStats' => $sourceStats,
            'recentLeads' => $recentLeads ?? [],
            'upcomingVisits' => $upcomingVisits ?? [],
        ]);
    }
}
