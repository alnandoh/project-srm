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
        Schema::create('tenders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('users')->onDelete('cascade');
            $table->string('name', 100);
            $table->string('special_preference', 255)->nullable();
            $table->string('food_type', 100);
            $table->decimal('budget', 10, 2);
            $table->text('note')->nullable();
            $table->integer('quantity')->unsigned();
            $table->timestamp('end_registration')->nullable();
            $table->timestamp('delivery_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenders');
    }
};
