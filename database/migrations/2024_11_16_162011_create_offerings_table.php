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
        Schema::create('offerings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tender_id')->constrained()->onDelete('cascade');
            $table->foreignId('vendor_id')->constrained('users')->onDelete('cascade');
            $table->string('title', 255);
            $table->text('description');
            $table->decimal('offer', 15, 2);
            $table->string('image', 255)->nullable();
            $table->decimal('delivery_cost', 15, 2);
            $table->enum('payment_type', ['dp', 'full'])->default('full');
            $table->decimal('dp_amount', 15, 2)->default(0)->change();
            $table->boolean('dp_paid')->default(false);
            $table->boolean('full_paid')->default(false);
            $table->enum('offering_status', [
                'pending', 'accepted', 'rejected', 'cancelled', 'completed'
            ])->default('pending');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offerings');
    }
};
