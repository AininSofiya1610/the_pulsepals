<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            [
                'name' => 'Ahmad Faizal',
                'company' => 'Maju Jaya Trading',
                'phone' => '+60 12-345 6789',
                'email' => 'ahmad.faizal@majujaya.com.my',
                'customerAddress' => '12, Jalan Tunku Abdul Rahman, 50100 Kuala Lumpur',
            ],
            [
                'name' => 'Sarah Lim',
                'company' => 'Sunrise Technologies',
                'phone' => '+60 16-789 0123',
                'email' => 'sarah.lim@sunrisetech.com.my',
                'customerAddress' => 'Unit 8-2, Q Sentral, Jalan Stesen Sentral 2, 50470 Kuala Lumpur',
            ],
            [
                'name' => 'Raj Kumar',
                'company' => 'Global Logistics Solution',
                'phone' => '+60 19-876 5432',
                'email' => 'raj.kumar@globallogistics.com.my',
                'customerAddress' => 'Lot 5, Jalan Pelabuhan Utara, 42000 Port Klang, Selangor',
            ],
            [
                'name' => 'Michelle Tan',
                'company' => 'Creative Design Studio',
                'phone' => '+60 12-987 6543',
                'email' => 'michelle.tan@creativedesign.com.my',
                'customerAddress' => 'B-3-1, Phileo Damansara 1, 46350 Petaling Jaya, Selangor',
            ],
            [
                'name' => 'Mohd Azlan',
                'company' => 'Azlan & Partners Law Firm',
                'phone' => '+60 3-2144 5566',
                'email' => 'azlan@azlanpartners.com.my',
                'customerAddress' => 'Level 15, Menara IMC, Jalan Sultan Ismail, 50250 Kuala Lumpur',
            ],
            [
                'name' => 'Jessica Wong',
                'company' => 'Healthy Living Sdn Bhd',
                'phone' => '+60 17-654 3210',
                'email' => 'jessica.wong@healthyliving.com.my',
                'customerAddress' => '18, Jalan Telawi 3, Bangsar Baru, 59100 Kuala Lumpur',
            ],
            [
                'name' => 'David Lee',
                'company' => 'Tech Innovations Corp',
                'phone' => '+60 3-7788 9900',
                'email' => 'david.lee@techinnovations.com',
                'customerAddress' => 'Cyberview Tower, Persiaran Multimedia, 63000 Cyberjaya, Selangor',
            ],
            [
                'name' => 'Nurul Huda',
                'company' => 'Bunga Raya Events',
                'phone' => '+60 13-555 6677',
                'email' => 'nurul.huda@bungarayaevents.com.my',
                'customerAddress' => '22, Jalan Damai, 55000 Kuala Lumpur',
            ],
            [
                'name' => 'Steven Chong',
                'company' => 'Chong Construction Works',
                'phone' => '+60 12-223 3445',
                'email' => 'steven.chong@chongconstruction.com.my',
                'customerAddress' => 'Lot 102, Kawasan Perindustrian Nilai, 71800 Nilai, Negeri Sembilan',
            ],
            [
                'name' => 'Fatimah Zainal',
                'company' => 'EduCare Learning Centre',
                'phone' => '+60 3-5633 4455',
                'email' => 'fatimah.zainal@educare.com.my',
                'customerAddress' => '45, Jalan SS 15/4, 47500 Subang Jaya, Selangor',
            ],
        ];

        // Check which columns exist
        $hasName = Schema::hasColumn('customers', 'name');
        $hasCustomerName = Schema::hasColumn('customers', 'customerName');
        $hasEmail = Schema::hasColumn('customers', 'email');
        $hasCustomerEmail = Schema::hasColumn('customers', 'customerEmail');
        $hasPhone = Schema::hasColumn('customers', 'phone');
        $hasCustomerPhone = Schema::hasColumn('customers', 'customerPhone');
        $hasCompany = Schema::hasColumn('customers', 'company');
        $hasAddress = Schema::hasColumn('customers', 'customerAddress');
        $hasStatus = Schema::hasColumn('customers', 'status');

        foreach ($customers as $data) {
            $customerData = [];
            
            // Handle Name
            if ($hasName) $customerData['name'] = $data['name'];
            if ($hasCustomerName) $customerData['customerName'] = $data['name'];
            
            // Handle Email
            if ($hasEmail) $customerData['email'] = $data['email'];
            if ($hasCustomerEmail) $customerData['customerEmail'] = $data['email'];
            
            // Handle Phone
            if ($hasPhone) $customerData['phone'] = $data['phone'];
            if ($hasCustomerPhone) $customerData['customerPhone'] = $data['phone'];
            
            // Handle Company
            if ($hasCompany) $customerData['company'] = $data['company'];
            
            // Handle Address
            if ($hasAddress) $customerData['customerAddress'] = $data['customerAddress'];
            
            // Handle Status
            if ($hasStatus) $customerData['status'] = 'Active';

            // Timestamps
            $customerData['created_at'] = Carbon::now()->subDays(rand(10, 100));
            $customerData['updated_at'] = Carbon::now();

            Customer::create($customerData);
        }
    }
}
