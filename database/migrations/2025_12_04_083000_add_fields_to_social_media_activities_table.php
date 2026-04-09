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
        Schema::table('social_media_activities', function (Blueprint $table) {
            if (!Schema::hasColumn('social_media_activities', 'activity_type')) {
                $table->string('activity_type')->nullable();
            }
            if (!Schema::hasColumn('social_media_activities', 'date')) {
                $table->date('date')->nullable();
            }

            // Make legacy columns nullable
            if (Schema::hasColumn('social_media_activities', 'post_date')) {
                $table->date('post_date')->nullable()->change();
            }
            if (Schema::hasColumn('social_media_activities', 'post_content')) {
                $table->text('post_content')->nullable()->change();
            }
            if (Schema::hasColumn('social_media_activities', 'followers_at_time')) {
                $table->integer('followers_at_time')->nullable()->change();
            }
            if (Schema::hasColumn('social_media_activities', 'engagement_rate')) {
                $table->decimal('engagement_rate', 5, 2)->nullable()->change();
            }
            
            // Ensure requested columns are present and nullable/compatible
             if (Schema::hasColumn('social_media_activities', 'platform')) {
                $table->string('platform')->nullable()->change();
            }
             if (Schema::hasColumn('social_media_activities', 'likes')) {
                $table->integer('likes')->nullable()->change();
            }
             if (Schema::hasColumn('social_media_activities', 'comments')) {
                $table->integer('comments')->nullable()->change();
            }
             if (Schema::hasColumn('social_media_activities', 'shares')) {
                $table->integer('shares')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('social_media_activities', function (Blueprint $table) {
            $table->dropColumn(['activity_type', 'date']);
        });
    }
};
