<?php

use App\Http\Controllers\Owner\ProjectController;
use App\Http\Controllers\Owner\PlotController;
use App\Http\Controllers\Owner\ExpenseController;
use App\Http\Controllers\Owner\AgentController;
use App\Http\Controllers\Owner\SearchController;
use App\Http\Controllers\Owner\DashboardController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\Owner\PaymentController;
use App\Http\Controllers\Owner\SiteVisitController as OwnerSiteVisitController;
use App\Http\Controllers\Agent\SiteVisitController as AgentSiteVisitController;
use App\Http\Controllers\Agent\DashboardController as AgentDashboardController;
use App\Http\Controllers\Buyer\PortalController;
use App\Http\Controllers\Public\AgentLinkController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Agent\LeadController;

use App\Mail\BuyerInvitation;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

Route::get('/test-email', function () {
    $buyer = User::factory()->create(['name' => 'Buyer 3']);
    Mail::to('itest2@example.com')->send(new BuyerInvitation($buyer, 'temporary123'));
    return 'Email sent! Check Mailtrap inbox.';
});


Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('/ref/{agent?}', [AgentLinkController::class, 'index'])->name('public.microsite');
Route::post('/capture-lead', [AgentLinkController::class, 'captureLead'])->name('public.capture-lead');

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::prefix('owner')
        ->middleware('role:superadmin,manager')
        ->name('owner.')
        ->group(function () {
            Route::get('/dashboard', [DashboardController::class, 'index'])
                ->name('dashboard');

            Route::get('/search', [SearchController::class, 'index'])->name('search');
            Route::post('/search/resolve', [SearchController::class, 'resolveConflict'])->name('search.resolve');

            Route::post('/sales/{sale}/payments', [PaymentController::class, 'store'])
                ->name('owner.sales.payments.store');

            Route::resource('projects', ProjectController::class)
                ->except(['show']);

            Route::get('/projects/{project}', [ProjectController::class, 'show'])
                ->name('projects.show');

            Route::prefix('projects/{project}')->group(function () {

                Route::get('/plots/create', [PlotController::class, 'create'])
                    ->name('plots.create');

                Route::post('/plots', [PlotController::class, 'store'])
                    ->name('plots.store');

                Route::get('/plots/{plot}', [PlotController::class, 'show'])
                    ->name('projects.plots.show');

                Route::get('/plots/{plot}/edit', [PlotController::class, 'edit'])
                    ->name('plots.edit');

                Route::get('/plots/{plot}/sell', [PlotController::class, 'sell'])
                    ->name('plots.sell');


                Route::post('/plots/{plot}/sales', [SaleController::class, 'store'])
                    ->name('plots.sell');

                Route::put('/plots/{plot}', [PlotController::class, 'update'])
                    ->name('plots.update');

                Route::delete('/plots/{plot}', [PlotController::class, 'destroy'])
                    ->name('plots.destroy');

                Route::get('/expenses/create', [ExpenseController::class, 'create'])
                    ->name('expenses.create');

                Route::post('/expenses', [ExpenseController::class, 'store'])
                    ->name('expenses.store');

                Route::get('/expenses/{expense}/edit', [ExpenseController::class, 'edit'])
                    ->name('expenses.edit');

                Route::put('/expenses/{expense}', [ExpenseController::class, 'update'])
                    ->name('expenses.update');

                Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy'])
                    ->name('expenses.destroy');

                Route::get('/agents', [AgentController::class, 'projectAgents'])
                    ->name('projects.agents');

                Route::post('/agents', [AgentController::class, 'assignAgent'])
                    ->name('projects.agents.assign');

                Route::put('/agents/{agent}', [AgentController::class, 'updateAgentCommission'])
                    ->name('projects.agents.update');

                Route::post('/agents/{agent}/bar', [AgentController::class, 'barAgent'])
                    ->name('projects.agents.bar');

                Route::post('/agents/{agent}/unbar', [AgentController::class, 'unbarAgent'])
                    ->name('projects.agents.unbar');

                Route::delete('/agents/{agent}', [AgentController::class, 'removeAgent'])
                    ->name('projects.agents.remove');

                Route::get('/site-visits/{visit}', [OwnerSiteVisitController::class, 'show'])
                    ->name('site-visits.show');

                Route::post('/site-visits/{visit}/confirm', [OwnerSiteVisitController::class, 'confirm'])
                    ->name('site-visits.confirm');

                Route::post('/site-visits/{visit}/complete', [OwnerSiteVisitController::class, 'complete'])
                    ->name('site-visits.complete');

                Route::post('/site-visits/{visit}/cancel', [OwnerSiteVisitController::class, 'cancel'])
                    ->name('site-visits.cancel');

                Route::get('/site-visits/checklist-suggestions', [OwnerSiteVisitController::class, 'getChecklistSuggestions'])
                    ->name('site-visits.checklist-suggestions');

                Route::get('/site-visits/available-slots', [OwnerSiteVisitController::class, 'getAvailableSlots'])
                    ->name('site-visits.available-slots');
            });

            Route::get('/site-visits/calendar', [OwnerSiteVisitController::class, 'calendar'])
                ->name('site-visits.calendar');

            Route::get('/agents', [AgentController::class, 'index'])
                ->name('agents.index');

            Route::get('/agents/create', [AgentController::class, 'create'])
                ->name('agents.create');

            Route::post('/agents', [AgentController::class, 'store'])
                ->name('agents.store');

            Route::get('/agents/{agent}/edit', [AgentController::class, 'edit'])
                ->name('agents.edit');

            Route::put('/agents/{agent}', [AgentController::class, 'update'])
                ->name('agents.update');

            Route::delete('/agents/{agent}', [AgentController::class, 'destroy'])
                ->name('agents.destroy');

            Route::get('/agents/{agent}', [AgentController::class, 'show'])->name('agents.show');
        });

    Route::prefix('agent')
        ->middleware('role:agent')
        ->name('agent.')
        ->group(function () {

            Route::get('/dashboard', [AgentDashboardController::class, 'index'])
                ->name('dashboard');

            Route::get('/site-visits', [AgentSiteVisitController::class, 'index'])
                ->name('site-visits.index');

            Route::get('/site-visits/create', [AgentSiteVisitController::class, 'create'])
                ->name('site-visits.create');

            Route::post('/site-visits', [AgentSiteVisitController::class, 'store'])
                ->name('site-visits.store');

            Route::get('/leads', [LeadController::class, 'index'])->name('leads.index');
            Route::get('/leads/{lead}', [LeadController::class, 'show'])->name('leads.show');
        });

    Route::prefix('buyer')
        ->middleware('role:buyer')
        ->name('buyer.')
        ->group(function () {
            Route::get('/portal', [PortalController::class, 'index'])->name('portal');
            Route::get('/installments', [PortalController::class, 'installments'])->name('installments');
            Route::post('/payments', [PortalController::class, 'uploadPayment'])->name('payments.upload');
            Route::get('/referral', [PortalController::class, 'referral'])->name('referral');
            Route::put('/profile', [PortalController::class, 'updateProfile'])->name('profile.update');
        });
});

require __DIR__ . '/settings.php';