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
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('bio')->nullable();
            $table->string('avatar')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('accepts_online_bookings')->default(true);
            $table->json('working_hours')->nullable(); // Store weekly schedule
            $table->json('break_times')->nullable(); // Store break times
            $table->decimal('commission_rate', 5, 2)->nullable(); // Commission percentage
            $table->timestamps();
            
            $table->index(['tenant_id', 'is_active']);
            $table->index(['tenant_id', 'accepts_online_bookings']);
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
