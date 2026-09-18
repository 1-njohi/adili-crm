<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\User;
use App\Services\LeadTetherService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AgentLinkController extends Controller
{
    protected $leadTetherService;

    public function __construct(LeadTetherService $leadTetherService)
    {
        $this->leadTetherService = $leadTetherService;
    }

    public function index($agentId = null)
    {
        $agent = null;
        $projects = collect();
        $singleProject = null;

        if ($agentId) {
            $agent = User::where('id', $agentId)->where('role', 'agent')->first();
        }

        if ($agent) {
            // Get all active projects assigned to this agent with active assignment
            $projects = $agent->projects()
                ->where('projects.status', 'active')           // ✅ qualify with table name
                ->wherePivot('status', 'active')              // only active assignments
                ->with(['plots' => function ($query) {
                    $query->where('plots.status', 'available'); // ✅ qualify with table name
                }])
                ->get();

            // If only one project, show it directly
            if ($projects->count() === 1) {
                $singleProject = $projects->first();
            }
        }

        if (! $agent || $projects->isEmpty()) {
            return Inertia::render('public/Microsite', [
                'agent' => $agent,
                'projects' => [],
                'singleProject' => null,
                'ref' => $agentId,
                'hasMultipleProjects' => false,
            ]);
        }

        \Log::info(count($projects));

        return Inertia::render('public/Microsite', [
            'agent' => $agent,
            'projects' => $projects,
            'singleProject' => $singleProject,
            'ref' => $agentId,
            'hasMultipleProjects' => $projects->count() > 1,
        ]);
    }

    public function captureLead(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'required|string|min:10',
            'email' => 'nullable|email',
            'name' => 'nullable|string|max:255',
            'agent_id' => 'required|exists:users,id',
            'project_id' => 'required|exists:projects,id',
            'source_platform' => 'nullable|string',
            'consent' => 'accepted',
            'fingerprint' => 'nullable|string',
        ]);

        $tether = $this->leadTetherService->createOrUpdateTether(
            phone: $validated['phone'],
            agentId: $validated['agent_id'],
            projectId: $validated['project_id'],
            source: 'microsite',
            sourcePlatform: $validated['source_platform'] ?? null,
            name: $validated['name'] ?? null,
            email: $validated['email'] ?? null,
            fingerprint: $validated['fingerprint'] ?? null,
        );

        return back()->with('success', 'Thank you! We\'ll be in touch shortly.');
    }
}
