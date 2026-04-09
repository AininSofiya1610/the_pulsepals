<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lead;
use App\Models\Customer;
use App\Models\Deal;
use Carbon\Carbon;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Deal::truncate();
        Customer::query()->delete();
        Lead::truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        echo "✅ Cleared existing data\n";
        
        // SCENARIO 1: Won Deal
        $lead1 = Lead::create([
            'name' => 'Ahmad - TechCorp Solutions',
            'email' => 'ahmad@techcorp.com.my',
            'phone' => '012-345-6789',
            'source' => 'Website',
            'status' => 'converted',
        ]);
        
        $customer1 = Customer::create([
            'name' => 'Ahmad Farhan',
            'email' => 'ahmad@techcorp.com.my',
            'phone' => '012-345-6789',
            'company' => 'TechCorp Solutions',
            'status' => 'active',
            'created_from_lead' => $lead1->id,
        ]);
        
        Deal::create([
            'customer_id' => $customer1->id,
            'title' => 'CRM System - RM 50k',
            'value' => 50000.00,
            'stage' => 'closed_won',
            'status' => 'won',
            'expected_close_date' => Carbon::now()->subDays(5),
        ]);
        
        echo "✅ Scenario 1: Won - Ahmad (CRM)\n";
        
        // SCENARIO 2: Negotiation
        $lead2 = Lead::create([
            'name' => 'Siti - My Business Sdn Bhd',
            'email' => 'siti@mybusiness.com',
            'phone' => '013-456-7890',
            'source' => 'Referral',
            'status' => 'converted',
        ]);
        
        $customer2 = Customer::create([
            'name' => 'Siti Nurhaliza',
            'email' => 'siti@mybusiness.com',
            'phone' => '013-456-7890',
            'company' => 'My Business Sdn Bhd',
            'status' => 'active',
            'created_from_lead' => $lead2->id,
        ]);
        
        Deal::create([
            'customer_id' => $customer2->id,
            'title' => 'E-Commerce Website - RM 35k',
            'value' => 35000.00,
            'stage' => 'negotiation',
            'status' => 'open',
            'expected_close_date' => Carbon::now()->addDays(7),
        ]);
        
        echo "✅ Scenario 2: Negotiation - Siti (E-Com)\n";
        
        // SCENARIO 3: New Opportunity
        $lead3 = Lead::create([
            'name' => 'Kumar - SmartTech',
            'email' => 'kumar@smarttech.my',
            'phone' => '014-567-8901',
            'source' => 'Cold Call',
            'status' => 'converted',
        ]);
        
        $customer3 = Customer::create([
            'name' => 'Kumar Raj',
            'email' => 'kumar@smarttech.my',
            'phone' => '014-567-8901',
            'company' => 'SmartTech Industries',
            'status' => 'active',
            'created_from_lead' => $lead3->id,
        ]);
        
        Deal::create([
            'customer_id' => $customer3->id,
            'title' => 'Mobile Inventory App - RM 45k',
            'value' => 45000.00,
            'stage' => 'new_opportunity',
            'status' => 'open',
            'expected_close_date' => Carbon::now()->addDays(30),
        ]);
        
        echo "✅ Scenario 3: New - Kumar (Mobile App)\n";
        
        // SCENARIO 4: Qualified Lead
        Lead::create([
            'name' => 'Fatimah - GreenEarth NGO',
            'email' => 'fatimah@greenearth.org',
            'phone' => '015-678-9012',
            'source' => 'LinkedIn',
            'status' => 'qualified',
        ]);
        
        echo "✅ Scenario 4: Qualified lead - Fatimah\n";
        
        // SCENARIO 5: Fresh Lead
        Lead::create([
            'name' => 'Michael - QuickServe',
            'email' => 'michael@quickserve.com',
            'phone' => '016-789-0123',
            'source' => 'Website',
            'status' => 'new',
        ]);
        
        echo "✅ Scenario 5: New lead - Michael\n\n";
        
        echo "========================================\n";
        echo "📊 SUMMARY\n";
        echo "========================================\n";
        echo "Leads: " . Lead::count() . "\n";
        echo "  New: " . Lead::where('status', 'new')->count() . "\n";
        echo "  Qualified: " . Lead::where('status', 'qualified')->count() . "\n";
        echo "  Converted: " . Lead::where('status', 'converted')->count() . "\n\n";
        echo "Customers: " . Customer::count() . "\n";
        echo "Deals: " . Deal::count() . "\n";
        echo "  New: " . Deal::where('stage', 'new_opportunity')->count() . "\n";
        echo "  Negotiation: " . Deal::where('stage', 'negotiation')->count() . "\n";
        echo "  Won: " . Deal::where('status', 'won')->count() . "\n\n";
        echo "Pipeline: RM " . number_format(Deal::where('status', 'open')->sum('value'), 0) . "\n";
        echo "========================================\n";
    }
}
