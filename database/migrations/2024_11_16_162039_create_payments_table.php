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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('tender_id')->constrained()->onDelete('cascade');
            $table->foreignId('vendor_id')->constrained('users')->onDelete('cascade');
            $table->string('invoice_image', 255)->nullable();
            $table->foreignId('delivery_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('amount', 15, 2)->default(0);
            $table->enum('payment_type', ['dp', 'full'])->default('full');
            $table->text('payment_notes')->nullable();
            $table->decimal('dp_amount', 15, 2)->default(0);
            $table->boolean('payment_status')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
