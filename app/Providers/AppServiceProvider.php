<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use App\Models\VendorFinance;
use App\Models\CustomerFinance;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // Share counts with sidebar - with safety checks
        View::composer('layouts.sidebar', function ($view) {
            try {
                $vendorFinancesCount = Schema::hasTable('vendor_finances') 
                    ? VendorFinance::count() 
                    : 0;
                    
                $customerFinancesCount = Schema::hasTable('customer_finances') 
                    ? CustomerFinance::count() 
                    : 0;
                    
                $view->with([
                    'vendorFinancesCount' => $vendorFinancesCount,
                    'customerFinancesCount' => $customerFinancesCount
                ]);
            } catch (\Exception $e) {
                $view->with([
                    'vendorFinancesCount' => 0,
                    'customerFinancesCount' => 0
                ]);
            }
        });
    }
}