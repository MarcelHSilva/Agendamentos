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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('duration'); // Duration in minutes
            $table->decimal('price', 10, 2);
            $table->string('color', 7)->default('#3B82F6'); // Hex color for calendar
            $table->boolean('is_active')->default(true);
            $table->boolean('online_booking_enabled')->default(true);
            $table->integer('buffer_time_before')->default(0); // Buffer time before in minutes
            $table->integer('buffer_time_after')->default(0); // Buffer time after in minutes
            $table->integer('advance_booking_min')->default(60); // Minimum advance booking in minutes
            $table->integer('advance_booking_max')->default(10080); // Maximum advance booking in minutes (7 days)
            $table->json('requirements')->nullable(); // Special requirements or notes
            $table->timestamps();
            
            $table->index(['tenant_id', 'is_active']);
            $table->index(['tenant_id', 'online_booking_enabled']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
