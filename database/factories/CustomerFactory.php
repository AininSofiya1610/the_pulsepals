<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    // ─── Malaysian-style name pools ──────────────────────────────────
    private const MALAY_FIRST = [
        'Ahmad', 'Muhammad', 'Mohd', 'Abdul', 'Ali', 'Ibrahim', 'Ismail',
        'Hassan', 'Husain', 'Omar', 'Yusuf', 'Faiz', 'Hafiz', 'Amir',
        'Azman', 'Aziz', 'Razak', 'Rizal', 'Shahrul', 'Syafiq', 'Fikri',
        'Nurul', 'Siti', 'Aisyah', 'Fatimah', 'Aminah', 'Zainab',
        'Nadia', 'Farah', 'Aida', 'Hanim', 'Safiya', 'Rina',
        'Hidayah', 'Syahira', 'Izzah', 'Nabila', 'Fatin', 'Liyana',
        'Aqilah', 'Balqis', 'Zahrah', 'Khairul', 'Haziq', 'Irfan',
    ];

    private const MALAY_LAST = [
        'bin Abdullah', 'bin Ismail', 'bin Razak', 'bin Hassan',
        'bin Ibrahim', 'bin Omar', 'bin Yusof', 'bin Ahmad',
        'bin Mohd', 'bin Ali', 'bin Hamid', 'bin Karim',
        'binti Abdullah', 'binti Ismail', 'binti Razak', 'binti Hassan',
        'binti Ibrahim', 'binti Omar', 'binti Yusof', 'binti Ahmad',
        'binti Mohd', 'binti Ali', 'binti Hamid', 'binti Karim',
    ];

    private const CHINESE_SURNAMES = [
        'Tan', 'Lim', 'Ng', 'Lee', 'Wong', 'Chan', 'Ong', 'Goh',
        'Koh', 'Chua', 'Yap', 'Teh', 'Ho', 'Foo', 'Cheah', 'Lau',
        'Sim', 'Chin', 'Heng', 'Low', 'Chong', 'Yeoh', 'Ooi', 'Soo',
    ];

    private const CHINESE_NAMES = [
        'Wei Liang', 'Jia Ying', 'Zhi Hao', 'Mei Ling', 'Jun Wei',
        'Xin Yi', 'Kai Wen', 'Shu Ting', 'Yong Sheng', 'Pei Shan',
        'Chee Keong', 'Siew Mei', 'Wai Kit', 'Hui Min', 'Zhi Xian',
        'Jing Wen', 'Yi Ling', 'Kok Leong', 'Soo Yin', 'Chun Kit',
        'Wen Hao', 'Li Hua', 'Boon Keat', 'Hui Yen', 'Kah Yee',
    ];

    private const INDIAN_FIRST = [
        'Rajesh', 'Kumar', 'Suresh', 'Arun', 'Vikram', 'Deepak',
        'Ganesh', 'Prasad', 'Ravi', 'Sanjay', 'Mohan', 'Karthik',
        'Priya', 'Devi', 'Lakshmi', 'Anitha', 'Kavitha', 'Shanti',
        'Malini', 'Vijaya', 'Revathi', 'Sangeetha', 'Nithya', 'Rani',
    ];

    private const INDIAN_LAST = [
        'Muthu', 'Suppiah', 'Krishnan', 'Rajan', 'Naidu', 'Pillai',
        'Nair', 'Subramaniam', 'Vellu', 'Muniandy', 'Arumugam',
        'Ramasamy', 'Thangaraj', 'Balakrishnan', 'Sinnappan',
    ];

    private const EMAIL_DOMAINS = [
        'gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com',
        'syarikat.com.my', 'company.my', 'enterprise.com.my',
        'bizsolutions.my', 'techworks.com.my', 'globalnet.my',
        'maxis.com.my', 'streamyx.com.my',
    ];

    private const COMPANIES = [
        'Petronas Dagangan', 'Tenaga Nasional Berhad', 'Sime Darby', 'Maybank',
        'CIMB Group', 'Public Bank', 'Axiata Group', 'Maxis Berhad',
        'Top Glove Corporation', 'Hartalega Holdings', 'Press Metal Aluminium',
        'IHH Healthcare', 'Genting Berhad', 'YTL Corporation', 'IOI Corporation',
        'Gamuda Berhad', 'Sunway Group', 'SP Setia', 'Eco World Development',
        'UEM Sunrise', 'Malaysia Airports Holdings', 'Astro Malaysia',
        'Telekom Malaysia', 'MISC Berhad', 'Sapura Energy',
        'Pavilion REIT', 'IGB REIT', 'Fraser & Neave Holdings',
        'Dutch Lady Milk Industries', 'Nestle Malaysia',
        'TechValley Solutions Sdn Bhd', 'Apex Digital Sdn Bhd',
        'Nusantara Systems Sdn Bhd', 'KL Consulting Group',
        'PJ Data Services', 'Cyberjaya Tech Hub Sdn Bhd',
        'Borneo Engineering Works', 'Sarawak Steel Fabricators',
        'Johor Port Solutions', 'Penang Precision Industries',
        'Selangor Smart Systems', 'Putrajaya Innovations',
        null, null, null, null, null, // ~10% null companies
    ];

    public function definition(): array
    {
        $name = $this->generateMalaysianName();
        $slug = strtolower(str_replace([' ', "'"], ['', ''], $name));
        $slug = preg_replace('/[^a-z0-9]/', '.', $slug);
        $slug = trim($slug, '.');

        // 90% have email, 10% null
        $hasEmail = $this->faker->boolean(90);
        $email = $hasEmail
            ? $slug . $this->faker->unique()->numberBetween(1, 9999) . '@' . $this->faker->randomElement(self::EMAIL_DOMAINS)
            : null;

        $phone = $this->generateMalaysianPhone();
        $company = $this->faker->randomElement(self::COMPANIES);
        $createdAt = $this->faker->dateTimeBetween('2024-01-01', '2026-12-31');

        // 80% Active, 20% Inactive
        $status = $this->faker->boolean(80) ? 'Active' : 'Inactive';

        return [
            'name'            => $name,
            'email'           => $email,
            'phone'           => $phone,
            'company'         => $company,
            'status'          => $status,
            'customerEmail'   => $email,
            'customerPhone'   => $phone,
            'customerAddress' => $company ? $this->generateMalaysianAddress() : null,
            'created_at'      => $createdAt,
            'updated_at'      => $createdAt,
        ];
    }

    private function generateMalaysianName(): string
    {
        $ethnicity = $this->faker->randomElement(['malay', 'malay', 'malay', 'chinese', 'chinese', 'indian']);

        return match ($ethnicity) {
            'malay'   => $this->faker->randomElement(self::MALAY_FIRST) . ' ' . $this->faker->randomElement(self::MALAY_LAST),
            'chinese' => $this->faker->randomElement(self::CHINESE_SURNAMES) . ' ' . $this->faker->randomElement(self::CHINESE_NAMES),
            'indian'  => $this->faker->randomElement(self::INDIAN_FIRST) . ' a/l ' . $this->faker->randomElement(self::INDIAN_LAST),
        };
    }

    private function generateMalaysianPhone(): string
    {
        $prefixes = ['011', '012', '013', '014', '016', '017', '018', '019'];
        $prefix = $this->faker->randomElement($prefixes);
        $number = $this->faker->numerify($prefix === '011' ? '########' : '#######');
        return '+60' . substr($prefix, 1) . '-' . $number;
    }

    private function generateMalaysianAddress(): string
    {
        $areas = [
            'Kuala Lumpur', 'Petaling Jaya', 'Shah Alam', 'Subang Jaya',
            'Cyberjaya', 'Putrajaya', 'Johor Bahru', 'George Town',
            'Kota Kinabalu', 'Kuching', 'Ipoh', 'Melaka', 'Seremban',
        ];
        $lot = $this->faker->numberBetween(1, 999);
        $jalan = 'Jalan ' . $this->faker->randomElement(['Utama', 'Sultan', 'Raja', 'Tun', 'Dato', 'Merdeka', 'Bukit Bintang']);
        $postcode = $this->faker->numberBetween(10000, 99999);
        $area = $this->faker->randomElement($areas);

        return "No. {$lot}, {$jalan}, {$postcode} {$area}";
    }
}
