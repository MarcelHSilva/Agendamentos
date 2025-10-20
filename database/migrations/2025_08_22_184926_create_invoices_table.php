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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_id')->nullable()->constrained()->onDelete('set null');
            $table->string('invoice_number')->unique(); // INV-2024-001
            $table->enum('type', ['subscription', 'one_time', 'refund'])->default('subscription');
            $table->enum('status', ['draft', 'pending', 'paid', 'failed', 'cancelled', 'refunded'])->default('pending');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->string('currency', 3)->default('BRL');
            $table->text('description')->nullable();
            $table->json('line_items')->nullable(); // Invoice items details
            $table->timestamp('due_date')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_method')->nullable(); // stripe, mercadopago, etc
            $table->string('payment_intent_id')->nullable();
            $table->string('stripe_invoice_id')->nullable();
            $table->string('mercadopago_payment_id')->nullable();
            $table->json('payment_metadata')->nullable();
            $table->timestamps();
            
            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'due_date']);
            $table->index('invoice_number');
            $table->index('stripe_invoice_id');
            $table->index('mercadopago_payment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
