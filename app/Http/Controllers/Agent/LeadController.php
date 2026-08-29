<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\LeadTether;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        $agentId = auth()->id();

        $query = LeadTether::where('agent_id', $agentId)
            ->with('project');

        // Filter by status
        if ($request->filter === 'active') {
            $query->where('expires_at', '>', now());
        } elseif ($request->filter === 'expired') {
            $query->where('expires_at', '<=', now());
        }

        // Search
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('phone_encrypted', 'like', '%' . $request->search . '%');
            });
        }

        $leads = $query->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Agent/Leads/Index', [
            'leads' => $leads,
            'filters' => [
                'filter' => $request->filter,
                'search' => $request->search,
            ],
        ]);
    }

    public function show(LeadTether $lead)
    {
        // Ensure the lead belongs to the authenticated agent
        if ($lead->agent_id !== auth()->id()) {
            abort(403);
        }

        $lead->load(['project', 'behavior', 'siteVisits']);

        return Inertia::render('Agent/Leads/Show', [
            'lead' => $lead,
        ]);
    }
}