<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lead;
use App\Models\Customer;
use App\Models\Deal;

class CleanLeadsSeeder extends Seeder
{
    /**
     * Create leads that align with existing sales pipeline
     */
    public function run(): void
    {
        echo "Creating aligned leads data...\n\n";
        
        // Get existing customers and deals
        $customers = Customer::with('deals')->get();
        
        // Create "converted" leads for existing customers that came from deals
        foreach ($customers->take(10) as $customer) {
            // Check if lead already exists
            $existingLead = Lead::where('email', $customer->email)->first();
            if (!$existingLead) {
                Lead::create([
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'phone' => $customer->phone ?? '010-000-0000',
                    'source' => 'Website',
                    'status' => 'converted',
                ]);
                echo "✅ Created converted lead: {$customer->name}\n";
            }
        }
        
        // Add some NEW leads (fresh inquiries - not yet contacted)
        $newLeads = [
            ['name' => 'Ali Rahman', 'email' => 'ali@newcompany.com', 'phone' => '012-111-2222', 'source' => 'Website'],
            ['name' => 'Sarah Lee', 'email' => 'sarah@startup.io', 'phone' => '013-222-3333', 'source' => 'LinkedIn'],
            ['name' => 'David Wong', 'email' => 'david@biztech.my', 'phone' => '014-333-4444', 'source' => 'Referral'],
        ];
        
        foreach ($newLeads as $leadData) {
            if (!Lead::where('email', $leadData['email'])->exists()) {
                Lead::create(array_merge($leadData, ['status' => 'new']));
                echo "✅ Created new lead: {$leadData['name']}\n";
            }
        }
        
        // Add some QUALIFIED leads (contacted and interested)
        $qualifiedLeads = [
            ['name' => 'Fatimah Zahra', 'email' => 'fatimah@greenorg.my', 'phone' => '015-444-5555', 'source' => 'Cold Call'],
            ['name' => 'Michael Tan', 'email' => 'michael@restaurant.com', 'phone' => '016-555-6666', 'source' => 'Website'],
        ];
        
        foreach ($qualifiedLeads as $leadData) {
            if (!Lead::where('email', $leadData['email'])->exists()) {
                Lead::create(array_merge($leadData, ['status' => 'qualified']));
                echo "✅ Created qualified lead: {$leadData['name']}\n";
            }
        }
        
        echo "\n========================================\n";
        echo "📊 LEADS SUMMARY\n";
        echo "========================================\n";
        echo "Total Leads: " . Lead::count() . "\n";
        echo "  - New: " . Lead::where('status', 'new')->count() . " (fresh inquiries)\n";
        echo "  - Qualified: " . Lead::where('status', 'qualified')->count() . " (interested)\n";
        echo "  - Converted: " . Lead::where('status', 'converted')->count() . " (now customers)\n";
        echo "========================================\n";
        echo "\n✨ Leads now aligned with sales pipeline!\n";
    }
}
