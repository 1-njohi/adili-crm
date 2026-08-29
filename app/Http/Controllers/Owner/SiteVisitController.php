<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\SiteVisit;
use App\Services\SiteVisitService;
use App\Services\LeadTetherService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SiteVisitController extends Controller
{
    protected $siteVisitService;
    protected $leadTetherService;

    public function __construct(SiteVisitService $siteVisitService, LeadTetherService $leadTetherService)
    {
        $this->siteVisitService = $siteVisitService;
        $this->leadTetherService = $leadTetherService;
    }

    public function calendar()
    {
        $visits = SiteVisit::where('status', 'confirmed')
            ->orWhere('status', 'pending_owner_confirmation')
            ->with(['project', 'bookedBy', 'attendingOwner'])
            ->get();

        return Inertia::render('Owner/SiteVisits/Calendar', [
            'visits' => $visits,
        ]);
    }

    public function show(Project $project, SiteVisit $visit)
    {
        if ($visit->project_id !== $project->id) {
            abort(404);
        }

        $visit->load(['project', 'bookedBy', 'attendingOwner', 'leadTether']);

        return Inertia::render('Owner/SiteVisits/Show', [
            'project' => $project,
            'visit' => $visit,
        ]);
    }

    public function confirm(Request $request, Project $project, SiteVisit $visit)
    {
        if ($visit->project_id !== $project->id) {
            abort(404);
        }

        $validated = $request->validate([
            'attending_owner_id' => 'required|exists:users,id',
            'checklist' => 'nullable|array',
        ]);

        $visit->attending_owner_id = $validated['attending_owner_id'];
        $visit->status = 'confirmed';
        $visit->checklist = $validated['checklist'] ?? [];
        $visit->save();

        return redirect()->route('owner.site-visits.show', [$project, $visit])
            ->with('success', 'Site visit confirmed successfully!');
    }

    public function complete(Request $request, Project $project, SiteVisit $visit)
    {
        if ($visit->project_id !== $project->id) {
            abort(404);
        }

        $validated = $request->validate([
            'feedback' => 'nullable|array',
            'next_steps' => 'nullable|string',
            'plot_id' => 'nullable|exists:plots,id',
        ]);

        $visit->feedback = $validated['feedback'] ?? [];
        $visit->notes = $validated['next_steps'] ?? null;
        $visit->status = 'completed';
        $visit->save();

        // If plot_id is provided (Send Formal Offer), create soft hold
        if ($validated['plot_id'] ?? false) {
            $this->siteVisitService->createSoftHold(
                $validated['plot_id'],
                $visit->lead_tether_id,
                $visit->id
            );
        }

        return redirect()->route('owner.site-visits.calendar')
            ->with('success', 'Site visit completed successfully!');
    }

    public function cancel(Project $project, SiteVisit $visit)
    {
        if ($visit->project_id !== $project->id) {
            abort(404);
        }

        $visit->status = 'cancelled';
        $visit->save();

        return redirect()->route('owner.site-visits.calendar')
            ->with('success', 'Site visit cancelled successfully!');
    }

    public function getChecklistSuggestions(Project $project)
    {
        $suggestions = $this->siteVisitService->getChecklistSuggestions($project);

        return response()->json($suggestions);
    }

    public function getAvailableSlots(Project $project, Request $request)
    {
        $date = $request->query('date') ? now()->parse($request->query('date')) : now();
        $slots = $this->siteVisitService->getAvailableSlots($project, $date);

        return response()->json($slots);
    }
}