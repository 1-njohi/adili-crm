<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\User;
use App\Models\LeadTether;
use App\Models\Plot;
use App\Models\Project;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Mail\BuyerInvitation;
use App\Mail\AgentNotification;
use Inertia\Inertia;
use Illuminate\Support\Facades\Crypt;

class SaleController extends Controller
{
    /**
     * Show the sale creation form for a specific plot
     */
    public function create(Project $project, Plot $plot)
    {
        return Inertia::render('Owner/Sales/Create', [
            'project' => $project,
            'plot' => $plot,
        ]);
    }

    /**
     * Store a newly created sale
     */
    public function store(Request $request, Project $project, Plot $plot)
    {
        $validated = $request->validate([
            'name' => 'required',
            'phone' => 'required|max:20',
            'email' => 'required|email',
            'deposit' => 'required|numeric|min:0',
            'months' => 'required|integer|min:1',
            'total_price' => 'required|numeric|min:0'
        ]);

        // Calculate monthly payment
        $monthlyPayment = ($validated['total_price'] - $validated['deposit']) / $validated['months'];

        // Check if buyer exists
        $buyer = User::where('email', $validated['email'])
            ->where('role', 'buyer')
            ->first();

        if (!$buyer) {
            // Check for lead tether by email
            $tether = LeadTether::where('email_encrypted', $validated['email'])->first();

            // Create new buyer user with temporary password
            $temporaryPassword = Str::random(16);
            $buyer = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'role' => 'buyer',
                'password' => Hash::make($temporaryPassword),
            ]);

            // Send buyer invitation with login details
            Mail::to($buyer->email)->send(new BuyerInvitation($buyer, $temporaryPassword));

            // If lead was tethered, notify the agent
            if ($tether) {
                Mail::to($tether->agent->email)->send(new AgentNotification(
                    $tether->agent,
                    $plot,
                    $buyer
                ));
            }
        }


        // Get agent from lead tether (if exists)
        $agentId = null;
        $emailEncrypted = Crypt::encryptString($validated['email']);
        $tether = LeadTether::where('email_encrypted', $emailEncrypted)->first();

        if ($tether && $tether->agent) {
            $agentId = $tether->agent->id;
            $commissionRate = $tether->agent->pivot->commission_rate ?? null;
            $commissionType = $tether->agent->pivot->commission_type ?? null;
        }

        $receiptPath = null;
        if ($request->hasFile('deposit_receipt')) {
            $receiptPath = $request->file('deposit_receipt')->store('receipts/deposits', 'public');
        }

        // Create the sale
        $sale = Sale::create([
            'plot_id' => $plot->id,
            'project_id' => $project->id,
            'buyer_id' => $buyer->id,
            'agent_id' => $agentId,
            'total_price' => $validated['total_price'],
            'deposit' => $validated['deposit'],
            'months' => $validated['months'],
            'monthly_payment' => $monthlyPayment,
            'remaining_balance' => $validated['total_price'] - $validated['deposit'],
            'payment_tier_selected' => $validated['months'] . ' months',
            'commission_rate' => $commissionRate ?? null,
            'commission_type' => $commissionType ?? null,
            'status' => 'active',
            'deposit_receipt_path' => $receiptPath
        ]);

        $sale->generateInstallments();

        // Create payment record for the deposit
        $payment = Payment::create([
            'sale_id' => $sale->id,
            'amount' => $validated['deposit'],
            'method' => 'Deposit', // You can make this dynamic later
            'receipt_path' => $receiptPath,
            'reconciled_at' => now(),
            'reconciled_by' => auth()->id(),
            'allocated_to' => 'deposit',
            'notes' => 'Initial deposit payment',
        ]);

        // Update plot status
        $plot->update(['status' => 'sold']);

        return redirect()->route('owner.projects.show', $project)
            ->with('success', 'Sale created successfully! Buyer has been notified.');
    }
}