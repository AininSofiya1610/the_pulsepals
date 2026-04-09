<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;

class CRMCustomerSeeder extends Seeder
{
    public function run()
    {
        $companies = [
            'Global Tech Corp', 'Summit Enterprises', 'Nexus Logics', 'Pinnacle Solutions', 
            'Quantum Dynamics', 'Horizon Systems', 'Velocity Group', 'Matrix Industries',
            'Blue Chip Data', 'Silver Line Services', 'Golden Gate Media', 'Iron Clad Security',
            'Swift Delivery Co', 'Urban Living Inc', 'Oceanic Explorations', 'Mountain Peak Gear',
            'Zenith Biotech', 'Infinity Software', 'Core Fiber Networks', 'Delta Heavy Machineries'
        ];

        $statuses = ['active', 'inactive', 'pending'];

        for ($i = 1; $i <= 50; $i++) {
            $firstName = ['James', 'Mary', 'John', 'Patricia', 'Robert', 'Jennifer', 'Michael', 'Linda', 'William', 'Elizabeth'][rand(0, 9)];
            $lastName = ['Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis', 'Rodriguez', 'Martinez'][rand(0, 9)];
            $name = $firstName . ' ' . $lastName;
            $company = $companies[rand(0, count($companies) - 1)];
            
            Customer::create([
                'name' => $name,
                'email' => strtolower($firstName . '.' . $lastName . $i . '@' . str_replace(' ', '', strtolower($company)) . '.com'),
                'phone' => '01' . rand(1, 9) . '-' . rand(1000000, 9999999),
                'company' => $company,
                'status' => $statuses[rand(0, 2)],
                // Backward compatibility fields
                'customerName' => $name,
                'customerEmail' => strtolower($firstName . '.' . $lastName . $i . '@' . str_replace(' ', '', strtolower($company)) . '.com'),
                'customerPhone' => '01' . rand(1, 9) . '-' . rand(1000000, 9999999),
                'customerAddress' => rand(1, 100) . ', Jalan Utama, ' . ['Kuala Lumpur', 'Penang', 'Johor Bahru', 'Ipoh', 'Malacca'][rand(0, 4)],
            ]);
        }
    }
}
