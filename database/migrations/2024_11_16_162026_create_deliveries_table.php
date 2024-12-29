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
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tender_id')->constrained()->onDelete('cascade');
            $table->foreignId('vendor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('offering_id')->nullable()->constrained('offerings')->onDelete('cascade');
            $table->string('shipping_track_number', 100);
            $table->string('courier', 100);
            $table->string('recipient_name', 100)->nullable();
            $table->string('recipient_phone', 50)->nullable();
            $table->text('qc_notes')->nullable();
            $table->integer('quantity_received')->nullable();
            $table->boolean('quality_check')->default(false);
            $table->boolean('quantity_check')->default(false);
            $table->enum('status', ['pending_payment', 'shipped', 'delivered', 'confirmed'])->default('pending_payment');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};
