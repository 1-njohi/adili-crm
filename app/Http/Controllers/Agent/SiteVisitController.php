<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\SiteVisit;
use App\Services\LeadTetherService;
use App\Services\SiteVisitService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SiteVisitController extends Controller
{
    protected $leadTetherService;

    protected $siteVisitService;

    public function __construct(LeadTetherService $leadTetherService, SiteVisitService $siteVisitService)
    {
        $this->leadTetherService = $leadTetherService;
        $this->siteVisitService = $siteVisitService;
    }

    public function index()
    {
        $visits = SiteVisit::where('booked_by_id', auth()->id())
            ->with(['project', 'attendingOwner'])
            ->orderBy('scheduled_at', 'asc')
            ->get();

        return Inertia::render('Agent/SiteVisits/Index', [
            'visits' => $visits,
        ]);
    }

    public function create()
    {
        $projects = Project::where('status', 'active')->get();

        return Inertia::render('Agent/SiteVisits/Create', [
            'projects' => $projects,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'scheduled_at' => 'required|date|after:now',
            'buyer_phone' => 'required|string|min:10',
            'buyer_name' => 'nullable|string|max:255',
            'buyer_email' => 'nullable|email',
            'buyer_attendee_count' => 'nullable|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        // Create or update tether
        $tether = $this->leadTetherService->createOrUpdateTether(
            phone: $validated['buyer_phone'],
            agentId: auth()->id(),
            projectId: $validated['project_id'],
            source: 'site_booking',
            name: $validated['buyer_name'] ?? null,
            email: $validated['buyer_email'] ?? null,
        );

        // Create site visit
        $visit = SiteVisit::create([
            'project_id' => $validated['project_id'],
            'booked_by_id' => auth()->id(),
            'lead_tether_id' => $tether->id,
            'scheduled_at' => $validated['scheduled_at'],
            'buyer_attendee_count' => $validated['buyer_attendee_count'] ?? 1,
            'notes' => $validated['notes'] ?? null,
            'status' => 'pending_owner_confirmation',
        ]);

        // Notify owners (will implement later)
        // NotificationService::notifyOwners($visit);

        return redirect()->route('agent.site-visits.index')
            ->with('success', 'Site visit requested! Waiting for owner confirmation.');
    }
}
