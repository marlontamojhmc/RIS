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
        Schema::create('location', function (Blueprint $table) {
            $table->id();

            // Foreign keys for hierarchical structure
            $table->unsignedBigInteger('region_id')->nullable();
            $table->unsignedBigInteger('province_id')->nullable();
            $table->unsignedBigInteger('municipality_id')->nullable();
            $table->unsignedBigInteger('barangay_id')->nullable();

            $table->timestamps();

            // Constraints (assuming related tables exist: regions, provinces, municipalities, barangays)
            $table->foreign('region_id')->references('id')->on('region')->onDelete('set null');
            $table->foreign('province_id')->references('id')->on('province')->onDelete('set null');
            $table->foreign('municipality_id')->references('id')->on('municipality')->onDelete('set null');
            $table->foreign('barangay_id')->references('id')->on('barangay')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
