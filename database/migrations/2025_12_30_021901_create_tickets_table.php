<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_id')->unique(); // TKT-20241230-1001
            $table->string('title');
            $table->text('description');
            
            // Contact Info
            $table->string('full_name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('phone_ext')->nullable();
            
            // Classification
            $table->foreignId('unit_id')->constrained()->onDelete('cascade');
            $table->enum('priority', ['Low', 'Medium', 'High', 'Critical'])->default('Medium');
            $table->enum('ticket_type', ['CM', 'PM'])->default('CM'); // Preventive/Corrective Maintenance
            $table->string('category'); // Hardware Issue, Software Issue, etc.
            
            // Status & Assignment
            $table->enum('status', ['Open', 'In Progress', 'Resolved', 'Closed'])->default('Open');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            
            // Timestamps
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
            
            // Indexes for performance
            $table->index('status');
            $table->index('priority');
            $table->index('ticket_type');
            $table->index('assigned_to');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tickets');
    }
};