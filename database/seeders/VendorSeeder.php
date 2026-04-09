<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vendor;
use Carbon\Carbon;

class VendorSeeder extends Seeder
{
    public function run(): void
    {
        $vendors = [
            [
                'vendorName' => 'TechSphere Solutions Sdn Bhd',
                'vendorPhone' => '+60 3-7123 4567',
                'vendorEmail' => 'contact@techsphere.com.my',
                'vendorAddress' => '15-2, Jalan PJU 5/1, Dataran Sunway, 47810 Petaling Jaya, Selangor',
                'created_at' => Carbon::now()->subDays(120),
            ],
            [
                'vendorName' => 'Pinnacle IT Services',
                'vendorPhone' => '+60 3-8765 4321',
                'vendorEmail' => 'sales@pinnacle-it.com.my',
                'vendorAddress' => '28, Jalan SS 15/4, 47500 Subang Jaya, Selangor',
                'created_at' => Carbon::now()->subDays(115),
            ],
            [
                'vendorName' => 'CloudMatrix Enterprise',
                'vendorPhone' => '+60 3-2181 9900',
                'vendorEmail' => 'hello@cloudmatrix.my',
                'vendorAddress' => 'Level 18, Menara CIMB, Jalan Stesen Sentral 2, 50470 Kuala Lumpur',
                'created_at' => Carbon::now()->subDays(100),
            ],
            [
                'vendorName' => 'DataPulse Technologies',
                'vendorPhone' => '+60 4-226 8800',
                'vendorEmail' => 'info@datapulse.com.my',
                'vendorAddress' => '12, Lorong Macallum, 10300 George Town, Pulau Pinang',
                'created_at' => Carbon::now()->subDays(95),
            ],
            [
                'vendorName' => 'Nexus Systems Integrators',
                'vendorPhone' => '+60 3-7890 1234',
                'vendorEmail' => 'support@nexus-si.com.my',
                'vendorAddress' => '3A-1, Plaza Sentral, Jalan Stesen Sentral 5, 50470 Kuala Lumpur',
                'created_at' => Carbon::now()->subDays(88),
            ],
            [
                'vendorName' => 'Orion Network Solutions',
                'vendorPhone' => '+60 7-333 4455',
                'vendorEmail' => 'sales@orionnet.com.my',
                'vendorAddress' => '45, Jalan Mutiara Emas 2, Taman Mount Austin, 81100 Johor Bahru',
                'created_at' => Carbon::now()->subDays(80),
            ],
            [
                'vendorName' => 'Quantum Innovations',
                'vendorPhone' => '+60 3-6201 7700',
                'vendorEmail' => 'hello@quantum-innov.com',
                'vendorAddress' => 'Unit 5-6, Oasis Square, Ara Damansara, 47301 Petaling Jaya',
                'created_at' => Carbon::now()->subDays(75),
            ],
            [
                'vendorName' => 'SilverLine Consulting',
                'vendorPhone' => '+60 3-2110 8800',
                'vendorEmail' => 'enquiry@silverline.com.my',
                'vendorAddress' => 'Level 22, Wisma UOA II, Jalan Pinang, 50450 Kuala Lumpur',
                'created_at' => Carbon::now()->subDays(68),
            ],
            [
                'vendorName' => 'Catalyst Digital Partners',
                'vendorPhone' => '+60 3-7722 8899',
                'vendorEmail' => 'contact@catalystdigital.my',
                'vendorAddress' => '18, Jalan PJS 11/28, Bandar Sunway, 46150 Petaling Jaya',
                'created_at' => Carbon::now()->subDays(60),
            ],
            [
                'vendorName' => 'Axiom Hardware Supplies',
                'vendorPhone' => '+60 3-5567 3344',
                'vendorEmail' => 'orders@axiomhw.com.my',
                'vendorAddress' => 'Lot 12, Jalan Industri 2, Kawasan Perindustrian Puchong, 47100 Puchong',
                'created_at' => Carbon::now()->subDays(55),
            ],
            [
                'vendorName' => 'ProTech Maintenance Services',
                'vendorPhone' => '+60 6-762 5500',
                'vendorEmail' => 'service@protech-ms.com.my',
                'vendorAddress' => '88, Jalan Melaka Raya 8, 75000 Melaka',
                'created_at' => Carbon::now()->subDays(50),
            ],
            [
                'vendorName' => 'Zenith Software Development',
                'vendorPhone' => '+60 3-8996 2200',
                'vendorEmail' => 'dev@zenithsoftware.my',
                'vendorAddress' => 'Block C, Cyberia SmartHomes, 63000 Cyberjaya, Selangor',
                'created_at' => Carbon::now()->subDays(45),
            ],
            [
                'vendorName' => 'BlueWave Communications',
                'vendorPhone' => '+60 3-2288 7700',
                'vendorEmail' => 'contact@bluewave.com.my',
                'vendorAddress' => '25-1, Jalan Ampang, 50450 Kuala Lumpur',
                'created_at' => Carbon::now()->subDays(38),
            ],
            [
                'vendorName' => 'Vertex Security Solutions',
                'vendorPhone' => '+60 3-7831 9900',
                'vendorEmail' => 'security@vertex-sol.com.my',
                'vendorAddress' => '9, Jalan 17/56, Section 17, 46400 Petaling Jaya',
                'created_at' => Carbon::now()->subDays(30),
            ],
            [
                'vendorName' => 'Emerald Office Supplies',
                'vendorPhone' => '+60 3-4043 6600',
                'vendorEmail' => 'sales@emeraldoffice.com.my',
                'vendorAddress' => '100, Jalan Genting Kelang, 53300 Setapak, Kuala Lumpur',
                'created_at' => Carbon::now()->subDays(25),
            ],
            [
                'vendorName' => 'Infinity Cloud Services',
                'vendorPhone' => '+60 3-2332 1100',
                'vendorEmail' => 'cloud@infinity-cs.com.my',
                'vendorAddress' => 'Level 15, The Gardens South Tower, Mid Valley City, 59200 Kuala Lumpur',
                'created_at' => Carbon::now()->subDays(20),
            ],
            [
                'vendorName' => 'Titan Electrical Trading',
                'vendorPhone' => '+60 3-6156 7788',
                'vendorEmail' => 'enquiry@titanelec.com.my',
                'vendorAddress' => '32, Jalan Industri 3/4, Rawang Integrated Industrial Park, 48000 Rawang',
                'created_at' => Carbon::now()->subDays(15),
            ],
            [
                'vendorName' => 'Synergy Training Academy',
                'vendorPhone' => '+60 3-7733 2244',
                'vendorEmail' => 'training@synergyacademy.my',
                'vendorAddress' => '6-2, Jalan SS 21/62, Damansara Utama, 47400 Petaling Jaya',
                'created_at' => Carbon::now()->subDays(10),
            ],
            [
                'vendorName' => 'Prime Logistics Solutions',
                'vendorPhone' => '+60 3-3176 8800',
                'vendorEmail' => 'ops@primelogistics.com.my',
                'vendorAddress' => 'Lot 5, Jalan Pelabuhan Utara, 42000 Port Klang, Selangor',
                'created_at' => Carbon::now()->subDays(5),
            ],
            [
                'vendorName' => 'Vanguard Printing Services',
                'vendorPhone' => '+60 3-9173 4455',
                'vendorEmail' => 'print@vanguard.com.my',
                'vendorAddress' => '77, Jalan Cheras, Taman Maluri, 55100 Kuala Lumpur',
                'created_at' => Carbon::now()->subDays(2),
            ],
        ];

        foreach ($vendors as $vendor) {
            Vendor::create($vendor);
        }
    }
}
