<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\VendorFinanceController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\Settings\LeadSourceController;


// ==========================================
// AUTH ROUTES
// ==========================================
require __DIR__.'/auth.php';

// Root redirect
Route::get('/', function () {
    if (!auth()->check()) return redirect()->route('login');
    if (auth()->user()->can('view dashboard')) return redirect()->route('dashboard');
    return redirect()->route('tickets.index');
});

// ==========================================
// PUBLIC TICKET ROUTES (no auth required)
// ==========================================
Route::prefix('ticket')->group(function () {
    Route::get('/{token}', [\App\Http\Controllers\TicketController::class, 'publicView'])->name('tickets.public.view');
    Route::post('/{token}/reply', [\App\Http\Controllers\TicketController::class, 'publicReply'])->name('tickets.public.reply');
});

// ==========================================
// PROTECTED ROUTES (auth required)
// ==========================================
Route::middleware(['auth'])->group(function () {

    Route::get('/account/pending', function () {
        return view('auth.pending');
    })->name('account.pending');

    // ==========================================
    // DASHBOARD
    // ==========================================
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('permission:view dashboard')
        ->name('dashboard');

    Route::get('/dashboard/report', [DashboardController::class, 'showReportForm'])
        ->middleware('permission:view dashboard')
        ->name('dashboard.report');

    Route::post('/dashboard/report/generate', [DashboardController::class, 'generateReport'])
        ->middleware('permission:view dashboard')
        ->name('dashboard.report.generate');

    Route::post('/dashboard/export', [DashboardController::class, 'export'])
        ->middleware('permission:view dashboard')
        ->name('dashboard.export');

    // ==========================================
    // EXPORT ROUTES
    // ==========================================

    // CRM exports
    Route::middleware(['permission:view-crm'])->group(function () {
        Route::post('/export/leads', [\App\Http\Controllers\ExportController::class, 'exportLeads'])->name('export.leads');
        Route::post('/export/crm-customers', [\App\Http\Controllers\ExportController::class, 'exportCrmCustomers'])->name('export.crm-customers');
        Route::post('/export/deals', [\App\Http\Controllers\ExportController::class, 'exportDeals'])->name('export.deals');
        Route::match(['get', 'post'], '/export/tasks', [\App\Http\Controllers\TaskController::class, 'export'])->name('export.tasks');
    });

    // Finance exports
    Route::middleware(['permission:view-finance'])->group(function () {
        Route::post('/export/finance-customers', [\App\Http\Controllers\ExportController::class, 'exportCustomers'])->name('export.finance-customers');
        Route::post('/export/company-finance', [\App\Http\Controllers\ExportController::class, 'exportCompanyFinance'])->name('export.company-finance');
        Route::post('/export/customer-finance', [\App\Http\Controllers\ExportController::class, 'exportCustomerFinance'])->name('export.customer-finance');
        Route::post('/export/vendor-finance', [\App\Http\Controllers\ExportController::class, 'exportVendorFinance'])->name('export.vendor-finance');
        Route::post('/export/vendors', [\App\Http\Controllers\VendorController::class, 'export'])->name('export.vendors');
    });

    // Ticket exports
    Route::post('/export/tickets', [\App\Http\Controllers\ExportController::class, 'exportTickets'])
        ->middleware('permission:view-all-tickets')
        ->name('export.tickets');

    // ==========================================
    // PERFORMANCE SNAPSHOT
    // ==========================================
    Route::get('/performance', [\App\Http\Controllers\PerformanceSnapshotController::class, 'index'])->name('performance.index');
    Route::get('/performance/itsm', [\App\Http\Controllers\PerformanceSnapshotController::class, 'itsm'])->name('performance.itsm');
    Route::post('/performance/itsm/export', [\App\Http\Controllers\PerformanceSnapshotController::class, 'export'])->name('performance.itsm.export');

    // ==========================================
    // FINANCE MODULE
    // ==========================================
    Route::middleware(['permission:view-finance'])->group(function () {

        // ── Vendor List ───────────────────────────────────────────────────
        Route::middleware(['permission:manage-vendors'])->group(function () {
            Route::get('/vendors/create', [VendorController::class, 'create'])->name('vendor.create');
            Route::post('/vendors', [VendorController::class, 'store'])->name('vendors.store');
            Route::put('/vendors/{id}', [VendorController::class, 'update'])->name('vendors.update');
            Route::delete('/vendors/{id}', [VendorController::class, 'destroy'])->name('vendors.destroy');
            Route::post('/vendors/update-paid', [VendorController::class, 'updatePaid'])->name('vendors.updatePaid');
            Route::get('/vendors/template', [VendorController::class, 'downloadTemplate'])->name('vendors.template');
            Route::post('/vendors/import', [VendorController::class, 'importExcel'])->name('vendors.import');
        });

        // ── Vendor Finance ────────────────────────────────────────────────
        Route::prefix('vendors/finance')->group(function () {
            Route::get('/', [VendorFinanceController::class, 'index'])->name('vendor.finance.index');
            Route::post('/', [VendorFinanceController::class, 'store'])->name('vendor.finance.store');
            Route::get('/template', [VendorFinanceController::class, 'downloadTemplate'])->name('vendor.finance.template');
            Route::post('/import', [VendorFinanceController::class, 'importExcel'])->name('vendor.finance.import');
            Route::post('/payment', [VendorFinanceController::class, 'paymentStore'])->name('vendor.finance.payment.store');
            Route::delete('/payment/{id}', [VendorFinanceController::class, 'paymentDestroy'])->name('vendor.finance.payment.destroy');
            Route::get('/{id}', [VendorFinanceController::class, 'show'])->name('vendor.finance.show');
            Route::delete('/{id}', [VendorFinanceController::class, 'destroy'])->name('vendor.finance.destroy');
        });

        // ── Finance Customer List ─────────────────────────────────────────
        // Completely separate from CRM Customers.
        // Uses CustomerController which internally uses
        // FinanceCustomer model → finance_customers table.
        Route::middleware(['permission:manage-customers'])->group(function () {
            Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create');
            Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
            Route::put('/customers/{id}', [CustomerController::class, 'update'])->name('customers.update'); // ← ADDED
            Route::delete('/customers/{id}', [CustomerController::class, 'destroy'])->name('customers.destroy');
            Route::get('/customers/template', [CustomerController::class, 'downloadCustomerTemplate'])->name('customers.template');
            Route::post('/customers/import', [CustomerController::class, 'importCustomerExcel'])->name('customers.import');
        });

        // ── Customer Finance (Invoices/Billing) ───────────────────────────
        Route::prefix('customers/finance')->group(function () {
            Route::get('/', [CustomerController::class, 'finance'])->name('customers.finance');
            Route::post('/', [CustomerController::class, 'financeStore'])->name('customers.finance.store');
            Route::get('/template', [CustomerController::class, 'downloadTemplate'])->name('customers.finance.template');
            Route::post('/import', [CustomerController::class, 'importExcel'])->name('customers.finance.import');
            Route::post('/payment', [CustomerController::class, 'paymentStore'])->name('customers.finance.payment.store');
            Route::delete('/payment/{id}', [CustomerController::class, 'paymentDestroy'])->name('customers.finance.payment.destroy');
            Route::post('/vendor/update', [CustomerController::class, 'updateVendorPayment'])->name('customers.finance.vendor.update');
            Route::get('/{id}', [CustomerController::class, 'financeShow'])->name('customers.finance.show');
            Route::put('/{id}', [CustomerController::class, 'financeUpdate'])->name('customers.finance.update');
            Route::delete('/{id}', [CustomerController::class, 'financeDestroy'])->name('customers.finance.destroy');
        });

        // ── Company Finance ───────────────────────────────────────────────
        Route::prefix('finance')->group(function () {
            Route::get('/company', [\App\Http\Controllers\CompanyFinanceController::class, 'index'])->name('finance.company.index');
            Route::post('/company', [\App\Http\Controllers\CompanyFinanceController::class, 'store'])->name('finance.company.store');
            Route::post('/company/export', [\App\Http\Controllers\CompanyFinanceController::class, 'export'])->name('finance.company.export');
        });

    }); // end view-finance

    // ==========================================
    // TICKETING SYSTEM (ITSM)
    // ==========================================
    Route::prefix('tickets')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\TicketController::class, 'dashboard'])->name('tickets.dashboard');
        Route::get('/', [\App\Http\Controllers\TicketController::class, 'index'])->name('tickets.index');
        Route::get('/my-tickets', [\App\Http\Controllers\TicketController::class, 'myTickets'])->name('tickets.my-tickets');
        Route::get('/{id}', [\App\Http\Controllers\TicketController::class, 'show'])->name('tickets.show');

        Route::middleware(['permission:create-ticket'])->group(function () {
            Route::get('/create', [\App\Http\Controllers\TicketController::class, 'create'])->name('tickets.create');
            Route::post('/', [\App\Http\Controllers\TicketController::class, 'store'])->name('tickets.store');
        });

        Route::middleware(['permission:update-ticket-status'])->group(function () {
            Route::get('/{id}/edit', [\App\Http\Controllers\TicketController::class, 'edit'])->name('tickets.edit');
            Route::put('/{id}', [\App\Http\Controllers\TicketController::class, 'update'])->name('tickets.update');
            Route::post('/{id}/activity', [\App\Http\Controllers\TicketController::class, 'addActivity'])->name('tickets.activity.add');
        });

        Route::middleware(['permission:assign ticket'])->group(function () {
            Route::post('/{id}/assign', [\App\Http\Controllers\TicketController::class, 'assignTicket'])->name('tickets.assign');
        });

        Route::middleware(['permission:delete-ticket'])->group(function () {
            Route::delete('/{id}', [\App\Http\Controllers\TicketController::class, 'destroy'])->name('tickets.destroy');
        });
    });

    // ==========================================
    // SETTINGS (Admin only)
    // ==========================================
    Route::middleware(['permission:access-settings'])->prefix('settings')->name('settings.')->group(function () {

        Route::get('/dropdown', [\App\Http\Controllers\TicketSettingsController::class, 'index'])->name('dropdown');
        Route::post('/dropdown', [\App\Http\Controllers\TicketSettingsController::class, 'storeOption'])->name('dropdown.store');
        Route::put('/dropdown/{option}', [\App\Http\Controllers\TicketSettingsController::class, 'updateOption'])->name('dropdown.update');
        Route::delete('/dropdown/{option}', [\App\Http\Controllers\TicketSettingsController::class, 'destroyOption'])->name('dropdown.destroy');

        Route::post('/units', [\App\Http\Controllers\TicketSettingsController::class, 'storeUnit'])->name('units.store');
        Route::put('/units/{unit}', [\App\Http\Controllers\TicketSettingsController::class, 'updateUnit'])->name('units.update');
        Route::delete('/units/{unit}', [\App\Http\Controllers\TicketSettingsController::class, 'destroyUnit'])->name('units.destroy');

        Route::get('/roles', [\App\Http\Controllers\RoleController::class, 'index'])->name('roles');
        Route::get('/roles/create', [\App\Http\Controllers\RoleController::class, 'create'])->name('roles.create');
        Route::post('/roles', [\App\Http\Controllers\RoleController::class, 'store'])->name('roles.store');
        Route::get('/roles/{role}/edit', [\App\Http\Controllers\RoleController::class, 'edit'])->name('roles.edit');
        Route::put('/roles/{role}', [\App\Http\Controllers\RoleController::class, 'update'])->name('roles.update');
        Route::delete('/roles/{role}', [\App\Http\Controllers\RoleController::class, 'destroy'])->name('roles.destroy');
        Route::get('/roles/{role}/permissions', [\App\Http\Controllers\RoleController::class, 'permissions'])->name('roles.permissions');
        Route::post('/roles/{role}/permissions', [\App\Http\Controllers\RoleController::class, 'updatePermissions'])->name('roles.permissions.update');

        Route::get('/users', [\App\Http\Controllers\UserController::class, 'index'])->name('users');
        Route::put('/users/{user}/role', [\App\Http\Controllers\UserController::class, 'updateRole'])->name('users.updateRole');
        Route::delete('/users/{user}', [\App\Http\Controllers\UserController::class, 'destroy'])->name('users.destroy');

        Route::get('/revenue-budgets', [\App\Http\Controllers\RevenueBudgetController::class, 'index'])->name('revenue-budgets.index');
        Route::post('/revenue-budgets', [\App\Http\Controllers\RevenueBudgetController::class, 'update'])->name('revenue-budgets.update');
        Route::post('/revenue-budgets/year', [\App\Http\Controllers\RevenueBudgetController::class, 'storeYear'])->name('revenue-budgets.year.store');
        Route::post('/revenue-budgets/export', [\App\Http\Controllers\RevenueBudgetController::class, 'export'])->name('revenue-budgets.export');
    });

    // ==========================================
    // MAINTENANCE
    // ==========================================
    Route::prefix('maintenance')->group(function () {
        Route::get('/pm-cm-report', [\App\Http\Controllers\MaintenanceController::class, 'pmCmReport'])->name('maintenance.pm-cm-report');
        Route::get('/unit-performance', [\App\Http\Controllers\MaintenanceController::class, 'unitPerformance'])->name('maintenance.unit-performance');
        Route::get('/reports', [\App\Http\Controllers\MaintenanceController::class, 'allReports'])->name('maintenance.reports');
    });

    // ==========================================
    // USER MANAGEMENT
    // ==========================================
    Route::middleware(['permission:manage-users'])->group(function () {
        Route::get('/users/technicians', [\App\Http\Controllers\UserManagementController::class, 'technicians'])->name('users.technicians');
        Route::get('/users', [\App\Http\Controllers\UserManagementController::class, 'index'])->name('users.index');
        Route::resource('units', \App\Http\Controllers\UnitController::class);
    });

    // ==========================================
    // MINI CRM MODULE
    // ==========================================
    Route::middleware(['permission:view-crm'])->group(function () {

        Route::get('/leads/suggestions', [\App\Http\Controllers\LeadController::class, 'searchSuggestions'])->name('leads.suggestions');

        Route::middleware(['permission:manage-leads'])->group(function () {
            Route::resource('leads', \App\Http\Controllers\LeadController::class);
            Route::post('leads/{id}/assign', [\App\Http\Controllers\LeadController::class, 'assign'])->name('leads.assign');
            Route::post('leads/{id}/status', [\App\Http\Controllers\LeadController::class, 'updateStatus'])->name('leads.updateStatus');

            // Lead → CRM Customer conversion ONLY.
            // ❌ Does NOT touch finance_customers table.
            Route::post('leads/{id}/convert', [\App\Http\Controllers\LeadController::class, 'convert'])->name('leads.convert');
        });

        // ── CRM Customers ─────────────────────────────────────────────────
        // Uses Customer model → customers table.
        // Completely separate from Finance Customers.
        Route::prefix('crm/customers')->group(function () {
            Route::get('/', [\App\Http\Controllers\CustomerController::class, 'index'])->name('crm.customers.index');
            Route::get('/{id}', [\App\Http\Controllers\CustomerController::class, 'show'])->name('crm.customers.show');
            Route::put('/{id}', [\App\Http\Controllers\CustomerController::class, 'update'])->name('crm.customers.update');
            Route::delete('/{id}', function ($id) {
                $customer = \App\Models\Customer::findOrFail($id);
                \App\Models\Deal::where('customer_id', $id)->delete();
                \App\Models\Activity::where('customer_id', $id)->delete();
                $customer->delete();
                return redirect()->route('crm.customers.index')->with('success', 'Customer deleted successfully!');
            })->name('crm.customers.destroy');
        });

        Route::middleware(['permission:manage-deals'])->group(function () {
            Route::resource('deals', \App\Http\Controllers\DealController::class);
            Route::post('deals/{id}/stage', [\App\Http\Controllers\DealController::class, 'updateStage'])->name('deals.updateStage');
        });

        Route::middleware(['permission:manage-tasks'])->group(function () {
            Route::resource('tasks', \App\Http\Controllers\TaskController::class);
            Route::post('tasks/{id}/status', [\App\Http\Controllers\TaskController::class, 'updateStatus'])->name('tasks.updateStatus');
        });

    }); // end view-crm

    // ==========================================
    // PROJECTS
    // ==========================================
    Route::get('/projects', [\App\Http\Controllers\ProjectController::class, 'index'])->name('projects.index');
    Route::post('/projects', [\App\Http\Controllers\ProjectController::class, 'store'])->name('projects.store');
    Route::post('/projects/export', [\App\Http\Controllers\ProjectController::class, 'export'])->name('projects.export');
    Route::get('/projects/{project}', [\App\Http\Controllers\ProjectController::class, 'show'])->name('projects.show');
    Route::put('/projects/{project}', [\App\Http\Controllers\ProjectController::class, 'update'])->name('projects.update');
    Route::post('/projects/{project}/stage', [\App\Http\Controllers\ProjectController::class, 'updateStage'])->name('projects.updateStage');
    Route::delete('/projects/{project}', [\App\Http\Controllers\ProjectController::class, 'destroy'])->name('projects.destroy');

    // ==========================================
    // ACTIVITIES
    // ==========================================
    Route::post('activities', [\App\Http\Controllers\ActivityController::class, 'store'])->name('activities.store');
    Route::delete('activities/{id}', [\App\Http\Controllers\ActivityController::class, 'destroy'])->name('activities.destroy');

    // ==========================================
    // PASSWORD
    // ==========================================
    Route::post('/change-password', [PasswordController::class, 'update'])
        ->name('change.password.update')
        ->middleware('auth');

}); // end auth middleware


Route::post('settings/lead-sources', [LeadSourceController::class, 'store'])->name('settings.lead-sources.store');
Route::delete('settings/lead-sources/{leadSource}', [LeadSourceController::class, 'destroy'])->name('settings.lead-sources.destroy');

// Lead Sources
Route::post('/lead-sources', [\App\Http\Controllers\TicketSettingsController::class, 'storeLeadSource'])->name('lead-sources.store');
Route::put('/lead-sources/{id}', [\App\Http\Controllers\TicketSettingsController::class, 'updateLeadSource'])->name('lead-sources.update');
Route::delete('/lead-sources/{id}', [\App\Http\Controllers\TicketSettingsController::class, 'destroyLeadSource'])->name('lead-sources.destroy');
