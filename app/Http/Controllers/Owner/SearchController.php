<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Services\LeadTetherService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SearchController extends Controller
{
    protected $leadTetherService;

    public function __construct(LeadTetherService $leadTetherService)
    {
        $this->leadTetherService = $leadTetherService;
    }

    public function index(Request $request)
    {
        $phone = $request->query('phone');
        $result = null;

        if ($phone) {
            $result = $this->leadTetherService->findTetherByPhone($phone);
        }

        return Inertia::render('owner/search/Index', [
            'phone' => $phone,
            'result' => $result,
        ]);
    }

    public function resolveConflict(Request $request)
    {
        $validated = $request->validate([
            'tether_id' => 'required|exists:lead_tethers,id',
            'agent_id' => 'required|exists:users,id',
        ]);

        $tether = $this->leadTetherService->resolveConflict(
            $validated['tether_id'],
            $validated['agent_id']
        );

        return redirect()->route('owner.search')
            ->with('success', 'Attribution resolved successfully!');
    }
}
