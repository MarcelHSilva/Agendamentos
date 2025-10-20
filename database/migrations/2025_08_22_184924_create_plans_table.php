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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Basic, Pro, Premium
            $table->string('slug')->unique(); // basic, pro, premium
            $table->text('description')->nullable();
            $table->decimal('price_monthly', 10, 2); // Monthly price
            $table->decimal('price_yearly', 10, 2)->nullable(); // Yearly price (with discount)
            $table->integer('max_staff')->default(1); // Maximum staff members
            $table->integer('max_clients')->default(100); // Maximum clients
            $table->integer('max_appointments_per_month')->default(50); // Monthly appointment limit
            $table->boolean('whatsapp_integration')->default(false);
            $table->boolean('email_notifications')->default(false);
            $table->boolean('online_booking')->default(false);
            $table->boolean('custom_branding')->default(false);
            $table->boolean('analytics_reports')->default(false);
            $table->boolean('api_access')->default(false);
            $table->json('features')->nullable(); // Additional features as JSON
            $table->integer('trial_days')->default(7); // Free trial period
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index('slug');
            $table->index('is_active');
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
