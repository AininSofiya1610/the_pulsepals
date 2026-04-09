<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_sources', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // Seed default lead sources to match existing hardcoded options
        $sources = [
            ['name' => 'Website',      'order' => 1],
            ['name' => 'Email Campaign', 'order' => 2],
            ['name' => 'Phone Call',   'order' => 3],
            ['name' => 'Social Media', 'order' => 4],
            ['name' => 'Referral',     'order' => 5],
            ['name' => 'Other',        'order' => 6],
        ];

        foreach ($sources as $source) {
            DB::table('lead_sources')->insert([
                'name'       => $source['name'],
                'order'      => $source['order'],
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_sources');
    }
};