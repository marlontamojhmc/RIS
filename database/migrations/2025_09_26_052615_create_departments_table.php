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
        Schema::create('departments', function (Blueprint $table) {
            $table->id();

            // Foreign key to divisions table (assuming it exists)
            //$table->unsignedBigInteger('division_id')->nullable();

            $table->string('department_code')->unique();
            $table->string('department_name');

            $table->timestamps();

            // Foreign key constraint
            // $table->foreign('division_id')
            //       ->references('id')
            //       ->on('divisions')
            //       ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
