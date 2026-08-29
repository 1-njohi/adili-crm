<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use App\Models\LeadTether;
use Illuminate\Support\Facades\Crypt;

class AgentController extends Controller
{
    public function show(User $agent)
    {
        if ($agent->role !== 'agent') {
            abort(404);
        }

        // Paginate leads (15 per page)
        $leads = $agent->leadTethers()
            ->with('project')
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->through(function ($lead) {
                // Decrypt phone and email
                try {
                    $lead->decrypted_phone = $lead->phone_encrypted ? Crypt::decryptString($lead->phone_encrypted) : null;
                } catch (\Exception $e) {
                    $lead->decrypted_phone = null;
                }
                try {
                    $lead->decrypted_email = $lead->email_encrypted ? Crypt::decryptString($lead->email_encrypted) : null;
                } catch (\Exception $e) {
                    $lead->decrypted_email = null;
                }
                return $lead;
            });

        return Inertia::render('owner/agents/Show', [
            'agent' => $agent,
            'leads' => $leads,
        ]);
    }
    public function index()
    {
        $agents = User::where('role', 'agent')
            ->withCount([
                'leadTethers as unique_leads_count' => function ($query) {
                    $query->select(\DB::raw('count(distinct phone_hash)'));
                }
            ])
            ->get();

        return Inertia::render('owner/agents/Index', [
            'agents' => $agents,
        ]);
    }

    public function create()
    {
        return Inertia::render('owner/agents/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'social_handles' => 'nullable|array',
            'bank_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'account_holder_name' => 'nullable|string|max:255',
        ]);

        $validated['role'] = 'agent';
        $validated['password'] = bcrypt('password123'); // Will be changed on first login

        $agent = User::create($validated);

        return redirect()->route('owner.agents.index')
            ->with('success', 'Agent created successfully!');
    }

    public function edit(User $agent)
    {
        if ($agent->role !== 'agent') {
            abort(404);
        }

        return Inertia::render('owner/agents/Edit', [
            'agent' => $agent,
        ]);
    }

    public function update(Request $request, User $agent)
    {
        if ($agent->role !== 'agent') {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($agent->id)],
            'phone' => 'nullable|string|max:20',
            'social_handles' => 'nullable|array',
            'bank_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'account_holder_name' => 'nullable|string|max:255',
        ]);

        $agent->update($validated);

        return redirect()->route('owner.agents.index')
            ->with('success', 'Agent updated successfully!');
    }

    public function destroy(User $agent)
    {
        if ($agent->role !== 'agent') {
            abort(404);
        }

        $agent->delete();

        return redirect()->route('owner.agents.index')
            ->with('success', 'Agent deleted successfully!');
    }

    // Agent-Project Assignment Management
    public function projectAgents(Project $project)
    {
        $availableAgents = User::where('role', 'agent')
            ->whereDoesntHave('projects', function ($query) use ($project) {
                $query->where('project_id', $project->id);
            })
            ->get();

        $assignedAgents = $project->agents;

        return Inertia::render('owner/projects/Agents', [
            'project' => $project,
            'assignedAgents' => $assignedAgents,
            'availableAgents' => $availableAgents,
        ]);
    }

    public function assignAgent(Request $request, Project $project)
    {
        $validated = $request->validate([
            'agent_id' => 'required|exists:users,id',
            'commission_type' => 'required|in:percentage,flat',
            'commission_rate' => 'required|numeric|min:0',
            'commission_currency' => 'nullable|string|size:3',
        ]);

        $agent = User::findOrFail($validated['agent_id']);

        if ($agent->role !== 'agent') {
            return back()->withErrors(['agent_id' => 'Selected user is not an agent.']);
        }

        $project->agents()->attach($agent->id, [
            'commission_type' => $validated['commission_type'],
            'commission_rate' => $validated['commission_rate'],
            'commission_currency' => $validated['commission_currency'] ?? 'KES',
            'status' => 'active',
        ]);

        return redirect()->route('owner.projects.agents', $project)
            ->with('success', 'Agent assigned to project successfully!');
    }

    public function updateAgentCommission(Request $request, Project $project, User $agent)
    {
        if ($agent->role !== 'agent') {
            abort(404);
        }

        // Check if agent has active buyers on this project
        $hasActiveBuyers = $agent->sales()
            ->where('project_id', $project->id)
            ->where('status', 'active')
            ->exists();

        if ($hasActiveBuyers) {
            return back()->withErrors([
                'commission' => 'Agent has active buyers. Commission cannot be changed.'
            ]);
        }

        $validated = $request->validate([
            'commission_type' => 'required|in:percentage,flat',
            'commission_rate' => 'required|numeric|min:0',
            'commission_currency' => 'nullable|string|size:3',
        ]);

        $project->agents()->updateExistingPivot($agent->id, [
            'commission_type' => $validated['commission_type'],
            'commission_rate' => $validated['commission_rate'],
            'commission_currency' => $validated['commission_currency'] ?? 'KES',
        ]);

        return redirect()->route('owner.projects.agents', $project)
            ->with('success', 'Commission updated successfully!');
    }

    public function barAgent(Request $request, Project $project, User $agent)
    {
        if ($agent->role !== 'agent') {
            abort(404);
        }

        // Check if agent has active buyers on this project
        $hasActiveBuyers = $agent->sales()
            ->where('project_id', $project->id)
            ->where('status', 'active')
            ->exists();

        $status = $hasActiveBuyers ? 'pending_removal' : 'inactive';

        $project->agents()->updateExistingPivot($agent->id, [
            'status' => $status,
        ]);

        $message = $hasActiveBuyers
            ? 'Agent barred from project. They can complete existing sales but cannot bring new leads.'
            : 'Agent barred from project successfully!';

        return redirect()->route('owner.projects.agents', $project)
            ->with('success', $message);
    }

    public function unbarAgent(Request $request, Project $project, User $agent)
    {
        if ($agent->role !== 'agent') {
            abort(404);
        }

        $project->agents()->updateExistingPivot($agent->id, [
            'status' => 'active',
        ]);

        return redirect()->route('owner.projects.agents', $project)
            ->with('success', 'Agent reinstated successfully!');
    }

    public function removeAgent(Request $request, Project $project, User $agent)
    {
        if ($agent->role !== 'agent') {
            abort(404);
        }

        // Check if agent has any sales on this project
        $hasSales = $agent->sales()
            ->where('project_id', $project->id)
            ->exists();

        if ($hasSales) {
            return back()->withErrors([
                'agent' => 'Agent has sales on this project. Cannot remove.'
            ]);
        }

        $project->agents()->detach($agent->id);

        return redirect()->route('owner.projects.agents', $project)
            ->with('success', 'Agent removed from project successfully!');
    }
}