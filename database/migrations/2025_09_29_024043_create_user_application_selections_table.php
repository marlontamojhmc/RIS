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
        Schema::create('user_application_selections', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('application_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('option_id');
            $table->timestamp('Expired_at')->nullable();
            $table->timestamp('selected_at')->nullable();
            $table->decimal('amount', 10, 2)->default(0.00);
            $table->timestamps();

            // Foreign keys
            $table->foreign('application_id')
                  ->references('id')->on('application_forms')
                  ->onDelete('cascade');

            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');

            $table->foreign('option_id')
                  ->references('id')->on('application_options')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_application_selections');
    }
};
