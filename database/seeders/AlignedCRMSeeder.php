<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lead;
use App\Models\Customer;
use App\Models\Deal;
use Carbon\Carbon;

class AlignedCRMSeeder extends Seeder
{
    /**
     * Create perfectly aligned demo data to show clear Lead → Deal workflow
     */
    public function run(): void
    {
        // Clear existing data
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Deal::truncate();
        Customer::query()->delete();
        Lead::truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        echo "\n🧹 Cleared existing data\n\n";
        echo "========================================\n";
        echo "📋 LEAD → DEAL ALIGNMENT DEMO\n";
        echo "========================================\n\n";
        
        // =============================================
        // SCENARIO 1: Fresh Lead (NEW)
        // =============================================
        echo "1️⃣  FRESH LEAD (NEW Status)\n";
        echo "   └─ Lead baru inquiry dari website\n";
        echo "   └─ Belum contact lagi\n";
        echo "   └─ NOT in pipeline yet\n\n";
        
        Lead::create([
            'name' => 'Ali Rahman - StartupHub',
            'email' => 'ali@startuphub.my',
            'phone' => '012-111-2222',
            'source' => 'Website Form',
            'status' => 'new',
        ]);
        
        Lead::create([
            'name' => 'Sarah Wong - BizTech',
            'email' => 'sarah@biztech.com',
            'phone' => '013-222-3333',
            'source' => 'LinkedIn',
            'status' => 'new',
        ]);
        
        // =============================================
        // SCENARIO 2: Contacted Lead
        // =============================================
        echo "2️⃣  CONTACTED LEAD\n";
        echo "   └─ Dah call/email, tengah follow up\n";
        echo "   └─ Belum qualify lagi\n";
        echo "   └─ NOT in pipeline yet\n\n";
        
        Lead::create([
            'name' => 'David Lim - QuickServe',
            'email' => 'david@quickserve.my',
            'phone' => '014-333-4444',
            'source' => 'Referral',
            'status' => 'contacted',
        ]);
        
        // =============================================
        // SCENARIO 3: Qualified Lead (Ready to Convert)
        // =============================================
        echo "3️⃣  QUALIFIED LEAD\n";
        echo "   └─ Dah verify: ada budget, timeline, decision maker\n";
        echo "   └─ READY to convert to Deal\n";
        echo "   └─ Belum create deal lagi\n\n";
        
        Lead::create([
            'name' => 'Fatimah Zahra - GreenEarth',
            'email' => 'fatimah@greenearth.org',
            'phone' => '015-444-5555',
            'source' => 'Cold Call',
            'status' => 'qualified',
        ]);
        
        Lead::create([
            'name' => 'Michael Tan - RestauHub',
            'email' => 'michael@restauranthub.com',
            'phone' => '016-555-6666',
            'source' => 'Website Form',
            'status' => 'qualified',
        ]);
        
        // =============================================
        // SCENARIO 4: Qualified Lead → CONVERTED → Deal (NEW Stage)
        // =============================================
        echo "4️⃣  CONVERTED LEAD → DEAL (NEW Stage)\n";
        echo "   └─ Lead: QUALIFIED → Click 'Convert'\n";
        echo "   └─ Deal created dengan value\n";
        echo "   └─ Deal masuk Pipeline: NEW column\n\n";
        
        $lead4 = Lead::create([
            'name' => 'Kumar Raj - SmartTech',
            'email' => 'kumar@smarttech.my',
            'phone' => '017-666-7777',
            'source' => 'Cold Call',
            'status' => 'converted', // ✅ Lead status changed to "converted"
        ]);
        
        $customer4 = Customer::create([
            'name' => 'Kumar Raj',
            'email' => 'kumar@smarttech.my',
            'phone' => '017-666-7777',
            'company' => 'SmartTech Industries',
            'status' => 'active',
            'created_from_lead' => $lead4->id,
        ]);
        
        Deal::create([
            'customer_id' => $customer4->id,
            'title' => 'Inventory Management System - RM 45k',
            'value' => 45000.00,
            'stage' => 'new_opportunity', // ✅ Deal starts at NEW stage
        ]);
        
        echo "   ✅ Lead: Kumar (Qualified) → Converted\n";
        echo "   ✅ Deal: RM 45k → Pipeline (NEW)\n\n";
        
        // =============================================
        // SCENARIO 5: Deal Progress - QUALIFIED Stage
        // =============================================
        echo "5️⃣  DEAL PROGRESS → QUALIFIED Stage\n";
        echo "   └─ Deal dari NEW → drag ke QUALIFIED\n";
        echo "   └─ Verify opportunity solid\n\n";
        
        $lead5 = Lead::create([
            'name' => 'Nurul Iman - CloudSoft',
            'email' => 'nurul@cloudsoft.my',
            'phone' => '018-777-8888',
            'source' => 'Website Form',
            'status' => 'converted',
        ]);
        
        $customer5 = Customer::create([
            'name' => 'Nurul Iman',
            'email' => 'nurul@cloudsoft.my',
            'phone' => '018-777-8888',
            'company' => 'CloudSoft Solutions',
            'status' => 'active',
            'created_from_lead' => $lead5->id,
        ]);
        
        Deal::create([
            'customer_id' => $customer5->id,
            'title' => 'HR Management Portal - RM 38k',
            'value' => 38000.00,
            'stage' => 'qualified', // ✅ Deal di QUALIFIED stage
        ]);
        
        echo "   ✅ Deal: Nurul (RM 38k) → QUALIFIED stage\n\n";
        
        // =============================================
        // SCENARIO 6: Deal Progress - PROPOSAL Stage
        // =============================================
        echo "6️⃣  DEAL PROGRESS → PROPOSAL Stage\n";
        echo "   └─ Dah hantar quotation\n";
        echo "   └─ Waiting customer review\n\n";
        
        $lead6 = Lead::create([
            'name' => 'Ahmad Farhan - TechCorp',
            'email' => 'ahmad@techcorp.com.my',
            'phone' => '019-888-9999',
            'source' => 'Referral',
            'status' => 'converted',
        ]);
        
        $customer6 = Customer::create([
            'name' => 'Ahmad Farhan',
            'email' => 'ahmad@techcorp.com.my',
            'phone' => '019-888-9999',
            'company' => 'TechCorp Solutions',
            'status' => 'active',
            'created_from_lead' => $lead6->id,
        ]);
        
        Deal::create([
            'customer_id' => $customer6->id,
            'title' => 'E-Commerce Platform - RM 52k',
            'value' => 52000.00,
            'stage' => 'proposal', // ✅ Deal di PROPOSAL stage
        ]);
        
        echo "   ✅ Deal: Ahmad (RM 52k) → PROPOSAL stage\n\n";
        
        // =============================================
        // SCENARIO 7: Deal Progress - NEGOTIATION Stage
        // =============================================
        echo "7️⃣  DEAL PROGRESS → NEGOTIATION Stage\n";
        echo "   └─ Tengah discuss terms & pricing\n";
        echo "   └─ Almost close!\n\n";
        
        $lead7 = Lead::create([
            'name' => 'Siti Nurhaliza - MyBiz',
            'email' => 'siti@mybiz.com.my',
            'phone' => '011-999-0000',
            'source' => 'Website Form',
            'status' => 'converted',
        ]);
        
        $customer7 = Customer::create([
            'name' => 'Siti Nurhaliza',
            'email' => 'siti@mybiz.com.my',
            'phone' => '011-999-0000',
            'company' => 'MyBiz Enterprise',
            'status' => 'active',
            'created_from_lead' => $lead7->id,
        ]);
        
        Deal::create([
            'customer_id' => $customer7->id,
            'title' => 'Mobile Delivery App - RM 65k',
            'value' => 65000.00,
            'stage' => 'negotiation', // ✅ Deal di NEGOTIATION stage
        ]);
        
        echo "   ✅ Deal: Siti (RM 65k) → NEGOTIATION stage\n\n";
        
        // =============================================
        // SCENARIO 8: Deal WON ✅
        // =============================================
        echo "8️⃣  DEAL WON ✅\n";
        echo "   └─ Customer agreed!\n";
        echo "   └─ Deal closed successfully\n";
        echo "   └─ Revenue confirmed!\n\n";
        
        $lead8 = Lead::create([
            'name' => 'James Chong - FinanceHub',
            'email' => 'james@financehub.my',
            'phone' => '012-000-1111',
            'source' => 'LinkedIn',
            'status' => 'converted',
        ]);
        
        $customer8 = Customer::create([
            'name' => 'James Chong',
            'email' => 'james@financehub.my',
            'phone' => '012-000-1111',
            'company' => 'FinanceHub Sdn Bhd',
            'status' => 'active',
            'created_from_lead' => $lead8->id,
        ]);
        
        Deal::create([
            'customer_id' => $customer8->id,
            'title' => 'Accounting Software Integration - RM 48k',
            'value' => 48000.00,
            'stage' => 'closed_won', // ✅ Deal WON!
        ]);
        
        echo "   ✅ Deal: James (RM 48k) → WON 🎉\n\n";
        
        // =============================================
        // SCENARIO 9: Deal LOST ❌
        // =============================================
        echo "9️⃣  DEAL LOST ❌\n";
        echo "   └─ Customer chose competitor\n";
        echo "   └─ Lesson learned\n\n";
        
        $lead9 = Lead::create([
            'name' => 'Lisa Tan - RetailPro',
            'email' => 'lisa@retailpro.my',
            'phone' => '013-111-2222',
            'source' => 'Cold Call',
            'status' => 'lost',
        ]);
        
        $customer9 = Customer::create([
            'name' => 'Lisa Tan',
            'email' => 'lisa@retailpro.my',
            'phone' => '013-111-2222',
            'company' => 'RetailPro Malaysia',
            'status' => 'inactive',
            'created_from_lead' => $lead9->id,
        ]);
        
        Deal::create([
            'customer_id' => $customer9->id,
            'title' => 'POS System - RM 28k',
            'value' => 28000.00,
            'stage' => 'closed_lost', // ❌ Deal LOST
        ]);
        
        echo "   ❌ Deal: Lisa (RM 28k) → LOST\n\n";
        
        // =============================================
        // SUMMARY
        // =============================================
        echo "========================================\n";
        echo "📊 SUMMARY - ALIGNED DATA\n";
        echo "========================================\n\n";
        
        echo "📋 LEADS (List Page):\n";
        echo "   • New: " . Lead::where('status', 'new')->count() . " leads (belum contact)\n";
        echo "   • Contacted: " . Lead::where('status', 'contacted')->count() . " leads (follow up)\n";
        echo "   • Qualified: " . Lead::where('status', 'qualified')->count() . " leads (ready to convert)\n";
        echo "   • Converted: " . Lead::where('status', 'converted')->count() . " leads (jadi deals)\n";
        echo "   • Lost: " . Lead::where('status', 'lost')->count() . " leads (gagal)\n";
        echo "   ─────────────────────────\n";
        echo "   TOTAL: " . Lead::count() . " leads\n\n";
        
        echo "💼 DEALS (Kanban Pipeline):\n";
        echo "   • New: " . Deal::where('stage', 'new_opportunity')->count() . " deals (baru convert)\n";
        echo "   • Qualified: " . Deal::where('stage', 'qualified')->count() . " deals (verified)\n";
        echo "   • Proposal: " . Deal::where('stage', 'proposal')->count() . " deals (hantar quotation)\n";
        echo "   • Negotiation: " . Deal::where('stage', 'negotiation')->count() . " deals (discuss terms)\n";
        echo "   • Won: " . Deal::where('stage', 'closed_won')->count() . " deals ✅\n";
        echo "   • Lost: " . Deal::where('stage', 'closed_lost')->count() . " deals ❌\n";
        echo "   ─────────────────────────\n";
        echo "   TOTAL: " . Deal::count() . " deals\n\n";
        
        echo "💰 PIPELINE VALUE:\n";
        $totalPipeline = Deal::whereIn('stage', ['new_opportunity', 'qualified', 'proposal', 'negotiation'])->sum('value');
        $wonValue = Deal::where('stage', 'closed_won')->sum('value');
        $lostValue = Deal::where('stage', 'closed_lost')->sum('value');
        
        echo "   • Active Pipeline: RM " . number_format($totalPipeline, 0) . "\n";
        echo "   • Won Revenue: RM " . number_format($wonValue, 0) . " ✅\n";
        echo "   • Lost Value: RM " . number_format($lostValue, 0) . " ❌\n\n";
        
        echo "========================================\n";
        echo "✨ ALIGNMENT COMPLETE!\n";
        echo "========================================\n\n";
        
        echo "📌 CLEAR CONNECTION:\n";
        echo "   Lead (NEW) → Lead (CONTACTED) → Lead (QUALIFIED)\n";
        echo "        ↓\n";
        echo "   [Convert to Deal]\n";
        echo "        ↓\n";
        echo "   Deal (NEW) → Deal (QUALIFIED) → Deal (PROPOSAL)\n";
        echo "        → Deal (NEGOTIATION) → Deal (WON/LOST)\n\n";
    }
}
