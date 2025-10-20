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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('appointment_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('client_id')->nullable()->constrained()->onDelete('cascade');
            $table->enum('channel', ['whatsapp', 'email', 'sms', 'push'])->default('whatsapp');
            $table->enum('type', ['reminder_24h', 'reminder_2h', 'confirmation', 'cancellation', 'rescheduled', 'custom']);
            $table->string('recipient'); // Phone, email, etc
            $table->string('subject')->nullable();
            $table->text('message');
            $table->json('template_data')->nullable(); // Data for template variables
            $table->enum('status', ['pending', 'sent', 'delivered', 'failed', 'read'])->default('pending');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->text('error_message')->nullable();
            $table->string('external_id')->nullable(); // WhatsApp/SMS provider message ID
            $table->integer('retry_count')->default(0);
            $table->timestamps();
            
            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'scheduled_at']);
            $table->index(['appointment_id', 'type']);
            $table->index('external_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
