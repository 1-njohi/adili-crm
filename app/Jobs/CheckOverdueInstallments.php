<?php

namespace App\Jobs;

use App\Models\Installment;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckOverdueInstallments implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    public function handle(): void
    {
        $overdueInstallments = Installment::where('status', 'pending')
            ->where('due_date', '<', now())
            ->get();

        foreach ($overdueInstallments as $installment) {
            $installment->markAsOverdue();

            // Optionally notify buyer and agent
            // event(new InstallmentOverdue($installment));
        }
    }
}
