<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\CommissionRelease;
use App\Models\Expense;
use App\Models\Installment;
use App\Models\LeadTether;
use App\Models\Payment;
use App\Models\Plot;
use App\Models\Project;
use App\Models\User;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        // ===== KEY METRICS =====
        // Revenue: Sum of all reconciled payments
        $totalRevenue = Payment::sum('amount');

        // Expenses: Sum of all project expenses
        $totalExpenses = Expense::sum('amount');

        // Commission paid: Sum of all commission releases (paid out)
        $totalCommissionPaid = CommissionRelease::where('status', 'paid')->sum('amount');

        // Commission pending: Sum of pending commission releases
        $totalCommissionPending = CommissionRelease::where('status', 'pending')->sum('amount');

        // Net Profit: Revenue - Expenses - Commission Paid
        $netProfit = $totalRevenue - $totalExpenses - $totalCommissionPaid;

        // Projects
        $totalProjects = Project::count();
        $activeProjects = Project::where('status', 'active')->count();
        $soldOutProjects = Project::where('status', 'sold_out')->count();

        // Plots
        $totalPlots = Plot::count();
        $soldPlots = Plot::where('status', 'sold')->count();
        $availablePlots = Plot::where('status', 'available')->count();
        $reservedPlots = Plot::where('status', 'reserved')->count();

        // Agents
        $totalAgents = User::where('role', 'agent')->count();

        // Leads
        $totalLeads = LeadTether::count();
        $activeLeads = LeadTether::where('expires_at', '>', now())->count();

        // ===== PROJECT SUMMARY =====
        $projects = Project::withCount(['plots', 'plots as sold_plots_count' => function ($query) {
            $query->where('status', 'sold');
        }])->get()->map(function ($project) {
            $project->progress = $project->plots_count > 0 ? round(($project->sold_plots_count / $project->plots_count) * 100, 2) : 0;

            return $project;
        });

        // ===== RECENT ACTIVITY =====
        // Recent Payments (last 10)
        $recentPayments = Payment::with(['sale.plot', 'sale.buyer'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Recent Leads (last 10)
        $recentLeads = LeadTether::with(['agent', 'project'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // ===== CASH FLOW FORECASTER =====
        // Upcoming installments due in next 30 days
        $upcomingInstallments = Installment::where('status', 'pending')
            ->whereBetween('due_date', [now(), now()->addDays(30)])
            ->with(['sale.plot', 'sale.buyer'])
            ->orderBy('due_date')
            ->get();

        $totalUpcomingAmount = $upcomingInstallments->sum('amount');

        // ===== QUICK ACTIONS =====
        // Provide quick action buttons

        return Inertia::render('owner/Dashboard', [
            'metrics' => [
                'total_revenue' => $totalRevenue,
                'total_expenses' => $totalExpenses,
                'total_commission_paid' => $totalCommissionPaid,
                'total_commission_pending' => $totalCommissionPending,
                'net_profit' => $netProfit,
                'total_projects' => $totalProjects,
                'active_projects' => $activeProjects,
                'sold_out_projects' => $soldOutProjects,
                'total_plots' => $totalPlots,
                'sold_plots' => $soldPlots,
                'available_plots' => $availablePlots,
                'reserved_plots' => $reservedPlots,
                'total_agents' => $totalAgents,
                'total_leads' => $totalLeads,
                'active_leads' => $activeLeads,
                'upcoming_installments_count' => $upcomingInstallments->count(),
                'upcoming_installments_amount' => $totalUpcomingAmount,
            ],
            'projects' => $projects,
            'recentPayments' => $recentPayments,
            'recentLeads' => $recentLeads,
            'upcomingInstallments' => $upcomingInstallments,
        ]);
    }
}
