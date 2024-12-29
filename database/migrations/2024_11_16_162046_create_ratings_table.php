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
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('tender_id')->constrained()->onDelete('cascade');
            $table->foreignId('offering_id')->constrained()->onDelete('cascade');
            $table->foreignId('delivery_id')->constrained()->onDelete('cascade');
            $table->integer('work_quality')->unsigned()->comment('Rating from 1-5')->default(0);
            $table->integer('timelines')->unsigned()->comment('Rating from 1-5')->default(0);
            $table->integer('communication')->unsigned()->comment('Rating from 1-5')->default(0);
            $table->enum('rating_type', ['vendor', 'admin'])->default('admin');
            $table->string('rated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
