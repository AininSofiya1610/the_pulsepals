<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vendor;

class VendorListSeeder extends Seeder
{
    public function run()
    {
        $vendors = [
            ['name' => 'Global Tech Solutions', 'phone' => '03-8912 3456', 'email' => 'contact@globaltech.com.my', 'address' => '12, Jalan Teknologi, Cyberjaya, Selangor'],
            ['name' => 'Summit Enterprises Sdn Bhd', 'phone' => '03-7845 6789', 'email' => 'info@summitent.com.my', 'address' => '45, Jalan Utama, Petaling Jaya, Selangor'],
            ['name' => 'Nexus Logistics (M) Sdn Bhd', 'phone' => '03-5678 1234', 'email' => 'ops@nexuslogistics.com.my', 'address' => '88, Jalan Industri, Shah Alam, Selangor'],
            ['name' => 'Pinnacle Systems Sdn Bhd', 'phone' => '03-2234 5678', 'email' => 'support@pinnaclesys.com.my', 'address' => '15, Jalan Raja Chulan, Kuala Lumpur'],
            ['name' => 'Quantum Dynamics Corp', 'phone' => '04-8912 3456', 'email' => 'sales@quantumdynamics.com', 'address' => '23, Lorong Perusahaan, Georgetown, Penang'],
            ['name' => 'Horizon Group Holdings', 'phone' => '07-3456 7890', 'email' => 'hq@horizongroup.com.my', 'address' => '67, Jalan Permas, Johor Bahru, Johor'],
            ['name' => 'Velocity Corp Sdn Bhd', 'phone' => '03-9012 3456', 'email' => 'info@velocitycorp.com.my', 'address' => '101, Jalan Ampang, Kuala Lumpur'],
            ['name' => 'Matrix Industries (M)', 'phone' => '05-2345 6789', 'email' => 'enquiry@matrixind.com.my', 'address' => '34, Jalan Sultan Iskandar, Ipoh, Perak'],
            ['name' => 'Blue Chip Data Services', 'phone' => '03-6789 0123', 'email' => 'data@bluechip.com.my', 'address' => '56, Jalan SS2/24, Petaling Jaya, Selangor'],
            ['name' => 'Silver Line Trading', 'phone' => '03-4567 8901', 'email' => 'trade@silverline.com.my', 'address' => '78, Jalan Pudu, Kuala Lumpur'],
            ['name' => 'Golden Gate Media Sdn Bhd', 'phone' => '03-8901 2345', 'email' => 'media@goldengate.com.my', 'address' => '22, Jalan Damansara, Kuala Lumpur'],
            ['name' => 'Iron Clad Security Services', 'phone' => '03-1234 5678', 'email' => 'security@ironclad.com.my', 'address' => '90, Jalan Kelang Lama, Kuala Lumpur'],
            ['name' => 'Swift Delivery Express', 'phone' => '03-5678 9012', 'email' => 'delivery@swiftexpress.com.my', 'address' => '44, Jalan Subang, Subang Jaya, Selangor'],
            ['name' => 'Urban Living Supplies', 'phone' => '03-9012 3456', 'email' => 'supply@urbanliving.com.my', 'address' => '11, Jalan Bangsar, Kuala Lumpur'],
            ['name' => 'Oceanic Explorations Sdn Bhd', 'phone' => '088-234 567', 'email' => 'explore@oceanic.com.my', 'address' => '33, Jalan Gaya, Kota Kinabalu, Sabah'],
            ['name' => 'Mountain Peak Gear Trading', 'phone' => '03-3456 7890', 'email' => 'gear@mountainpeak.com.my', 'address' => '55, Jalan Bukit Bintang, Kuala Lumpur'],
            ['name' => 'Zenith Biotech (M) Sdn Bhd', 'phone' => '03-7890 1234', 'email' => 'bio@zenithbiotech.com.my', 'address' => '77, Jalan Teknologi 5, Cyberjaya, Selangor'],
            ['name' => 'Infinity Software Solutions', 'phone' => '03-2345 6789', 'email' => 'dev@infinitysoft.com.my', 'address' => '99, Jalan Multimedia, Cyberjaya, Selangor'],
            ['name' => 'Core Fiber Networks Sdn Bhd', 'phone' => '03-6789 0123', 'email' => 'network@corefiber.com.my', 'address' => '21, Jalan Telekom, Bangsar South, Kuala Lumpur'],
            ['name' => 'Delta Heavy Machineries', 'phone' => '03-0123 4567', 'email' => 'sales@deltaheavy.com.my', 'address' => '66, Jalan Industri 3, Port Klang, Selangor'],
            ['name' => 'Alpha Consulting Group', 'phone' => '03-4567 8901', 'email' => 'consult@alphacg.com.my', 'address' => '88, Jalan Tun Razak, Kuala Lumpur'],
            ['name' => 'Beta Manufacturing Sdn Bhd', 'phone' => '03-8901 2345', 'email' => 'factory@betamfg.com.my', 'address' => '42, Jalan Perindustrian, Puchong, Selangor'],
            ['name' => 'Gamma Electronics Trading', 'phone' => '03-2345 6789', 'email' => 'electronics@gammaet.com.my', 'address' => '18, Jalan Imbi, Kuala Lumpur'],
            ['name' => 'Omega Services Sdn Bhd', 'phone' => '03-6789 0123', 'email' => 'service@omegasvcs.com.my', 'address' => '35, Jalan Syed Putra, Kuala Lumpur'],
            ['name' => 'Prime Solutions Asia', 'phone' => '03-0123 4567', 'email' => 'asia@primesolutions.com', 'address' => '72, Jalan Sultan Ismail, Kuala Lumpur'],
        ];

        foreach ($vendors as $vendor) {
            Vendor::create([
                'vendorName' => $vendor['name'],
                'vendorPhone' => $vendor['phone'],
                'vendorEmail' => $vendor['email'],
                'vendorAddress' => $vendor['address'],
                'total_paid' => rand(5000, 500000),
            ]);
        }
    }
}
