<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('website_visitors', function (Blueprint $table) {
            if (!Schema::hasColumn('website_visitors', 'page_visited')) {
                $table->string('page_visited')->nullable();
            }
            if (!Schema::hasColumn('website_visitors', 'visitor_ip')) {
                $table->string('visitor_ip')->nullable();
            }
            if (!Schema::hasColumn('website_visitors', 'date')) {
                $table->date('date')->nullable();
            }

            // Make legacy columns nullable
            if (Schema::hasColumn('website_visitors', 'visit_date')) {
                $table->date('visit_date')->nullable()->change();
            }
            if (Schema::hasColumn('website_visitors', 'unique_visitors')) {
                $table->integer('unique_visitors')->nullable()->change();
            }
            if (Schema::hasColumn('website_visitors', 'page_views')) {
                $table->integer('page_views')->nullable()->change();
            }
            if (Schema::hasColumn('website_visitors', 'source')) {
                $table->string('source')->nullable()->change();
            }
            if (Schema::hasColumn('website_visitors', 'notes')) {
                $table->text('notes')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('website_visitors', function (Blueprint $table) {
            $table->dropColumn(['page_visited', 'visitor_ip', 'date']);
        });
    }
};
