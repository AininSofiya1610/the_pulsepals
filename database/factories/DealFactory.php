<?php

namespace Database\Factories;

use App\Models\Deal;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class DealFactory extends Factory
{
    protected $model = Deal::class;

    private const DEAL_TITLES = [
        'IT Infrastructure Upgrade',
        'Cloud Migration Project',
        'ERP System Implementation',
        'Cybersecurity Assessment',
        'Network Overhaul',
        'Data Centre Setup',
        'Software Licensing Renewal',
        'Managed Services Contract',
        'Helpdesk Support Agreement',
        'Office 365 Deployment',
        'VPN Implementation',
        'Firewall Upgrade',
        'Server Consolidation',
        'Backup & Recovery Solution',
        'Digital Transformation Project',
        'IoT Sensor Deployment',
        'AI Analytics Platform',
        'Mobile App Development',
        'Website Redesign',
        'CRM System Setup',
        'POS System Installation',
        'CCTV & Security System',
        'Printing Solutions Contract',
        'Telephony System Upgrade',
        'Wi-Fi Infrastructure Project',
        'Annual Maintenance Contract',
        'Hardware Procurement',
        'Custom Software Development',
        'Database Migration',
        'Compliance Audit System',
    ];

    public function definition(): array
    {
        $stage = $this->faker->randomElement([
            'new_opportunity', 'qualified', 'proposal',
            'negotiation', 'closed_won', 'closed_lost',
        ]);

        $closedReason = null;
        if ($stage === 'closed_won') {
            $closedReason = $this->faker->randomElement([
                'Competitive pricing', 'Strong proposal', 'Good relationship',
                'Met all requirements', 'Fastest delivery',
            ]);
        } elseif ($stage === 'closed_lost') {
            $closedReason = $this->faker->randomElement([
                'Budget constraints', 'Chose competitor', 'Project cancelled',
                'Timing not right', 'Requirements changed',
            ]);
        }

        $createdAt = $this->faker->dateTimeBetween('2024-01-01', '2026-12-31');

        return [
            'customer_id'   => Customer::factory(),
            'title'         => $this->faker->randomElement(self::DEAL_TITLES),
            'value'         => $this->faker->randomFloat(2, 5000, 500000),
            'stage'         => $stage,
            'closed_reason' => $closedReason,
            'created_at'    => $createdAt,
            'updated_at'    => $createdAt,
        ];
    }
}
